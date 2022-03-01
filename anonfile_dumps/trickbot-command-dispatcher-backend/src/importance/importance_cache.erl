-module(importance_cache).

%% API
-export([
	init/0, update/0, pid/0,
	start_link/0,
	rules/1
]).

-include("importance.hrl").

-define(CACHE_MODULE, importance_cache_data).
-define(UPDATE_INTERVAL, cmd_server_app:env(importance_cache_update_interval) * 1000).

init() ->
	{ok,_ } = cmd_server_sup:start_child({ ?MODULE, { ?MODULE, start_link, [] }, permanent, 2000, worker, [ ?MODULE ] }).

update() ->
	pid() ! update.

pid() ->
	Children = supervisor:which_children(cmd_server_sup),
	{importance_cache, Pid, _, _} = lists:keyfind(?MODULE, 1, Children),
	Pid.

start_link() ->
	Pid = spawn_link(fun Loop() ->
		Rules = get_rules(),
		lager:info("Update importance rules"),
		term_compiler:compile(?CACHE_MODULE, [{ rules, {list, Rules ++ [ {undefined, undefined } ]}}]),
		receive
			update -> ok
			after ?UPDATE_INTERVAL -> ok
		end,
		Loop()
	end),
	{ ok, Pid }.
	
rules(Class) ->
	try
		?CACHE_MODULE:rules(Class)
	catch
		error:function_clause -> []
	end.

get_rules() ->
	SQL = "SELECT id, class, params, preplus, mul, postplus FROM importance_rules ORDER BY class asc, id desc ",
	{ ok, [ <<"id">>, <<"class">>, <<"params">>, <<"preplus">>, <<"mul">>, <<"postplus">> ], Rows } = db:squery(SQL),
	lists:foldl(fun({Id, Class, ParamsBin, Preplus, Mul, Postplus}, Acc) ->
		case importance_parser:parse(Class, ParamsBin) of
			undefined -> Acc;
			{ ok, Params } ->
				Importance = #importance{
					id = Id,
					class = Class,
					params = Params,
					params_bin = from_null(ParamsBin),
					postplus = to_float(Postplus),
					mul = to_float(Mul),
					preplus = to_float(Preplus)
				},
				case Acc of
					[{Group, List0}|List1] when Group =:= Class ->
						[{Group, [ Importance | List0 ]}|List1];
					List ->
						[{Class, [ Importance ]}|List]
				end
		end
	end, [], Rows).

to_float(Bin) when is_binary(Bin) ->
	try
		binary_to_float(Bin)
	catch
		error:badarg ->
			binary_to_integer(Bin)
	end.
	
from_null(null) -> <<>>;
from_null(Any) -> Any.
	

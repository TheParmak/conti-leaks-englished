-module(client_event_commands_cache).

-include("client_command.hrl").
-include("client.hrl").

%% API
-export([
	init/0, update/0, pid/0,
	start_link/0,
	command/4
]).

-record(rule, {
	id :: pos_integer(),
	module :: binary(),
	event :: binary(),
	re :: re:mp(),
	incode :: integer(),
	params :: binary(),
	interval :: integer()
}).

-define(CACHE_MODULE, commands_event_cache_data).
-define(UPDATE_INTERVAL, cmd_server_app:env(commands_event_cache_update_interval) * 1000).
-define(ETS, commands_event_timeouts).

init() ->
	{ok,_ } = cmd_server_sup:start_child({ ?MODULE, { ?MODULE, start_link, [] }, permanent, 2000, worker, [ ?MODULE ] }).

update() ->
	pid() ! update.

pid() ->
	Children = supervisor:which_children(cmd_server_sup),
	{?MODULE, Pid, _, _} = lists:keyfind(?MODULE, 1, Children),
	Pid.

start_link() ->
	Pid = spawn_link(fun() ->
		?ETS = ets:new(?ETS, [named_table, public, set, { write_concurrency, true }, { read_concurrency, true }]),
	  (fun Loop() ->
			Rules = get_events(),
			%% lager:info("Update commands for events"),
			term_compiler:compile(?CACHE_MODULE, [{ command, {list, Rules ++ [{ undefined, undefined }]}}]),
			receive
				update -> ok
				after ?UPDATE_INTERVAL -> ok
			end,
			Now = time:now(),
			ets:foldl(fun({Key, Time}, _) ->
				case Time =< Now of
					true -> ets:delete(?ETS, Key);
					false -> ok
				end
			end, ok, ?ETS),
			Loop()
		end)()
	end),
	{ ok, Pid }.

command(Client,Module, Event, Info) ->
	try
		Data = ?CACHE_MODULE:command({Module, Event}),
		R0 = lists:map(fun(Item = #rule{ incode = Incode, params = Params }) ->
			{ Item, #client_command{
				incode = Incode,
				params = Params,
				client_id = Client#client.id
			}}
		end, Data),
		Now = time:now(),
		R1 = lists:filter(fun({Rule, Command}) ->
			case re:run(Info, Rule#rule.re) of
				{ match, _ } ->
					can_be_assigned(Client, Rule, Command, Now);
				nomatch -> false
			end
		end, R0),
		lists:map(fun({Rule, Command}) ->
	    assign(Client, Command, Rule, Now),
	    Command
	  end, R1)
	catch
		error:function_clause -> []
	end.

can_be_assigned(_Client, #rule{ interval = 0 }, _Command, _) -> true;
can_be_assigned(Client, Rule, _Command, Now) ->
	Key = { Client#client.id, Rule#rule.id },
	case ets:lookup(?ETS, Key) of
		[] -> true;
		[{_, Time}] ->
			Now >= Time
	end.

assign(_, _, #rule{ interval = 0 }, _) -> ok;
assign(Client, _Command, Rule, Now) ->
	Key = { Client#client.id, Rule#rule.id },
	Value = Now + Rule#rule.interval,
	ets:insert(?ETS, { Key, Value }),
	ok.

get_events() ->
	SQL = <<"SELECT id, module, event, info, incode, params, interval FROM commands_event ORDER BY id ASC">>,
	{ ok, [ <<"id">>, <<"module">>, <<"event">>, <<"info">>, <<"incode">>, <<"params">>, <<"interval">> ], Rows } = db:squery(SQL),
	D = lists:foldl(fun({Id, Module, Event, Info, Incode, Params, Interval}, Dict) ->
		try
			{ ok, Re } = re:compile(Info, [ unicode ]),
			Rule = #rule{
				id = Id,
				module = Module,
				event = Event,
				re = Re,
				incode = binary_to_integer(Incode),
				params = Params,
				interval = case Interval of
					null -> 0;
					<<>> -> 0;
					_ -> binary_to_integer(Interval)
				end
			},
			dict:append({Module, Event}, Rule, Dict)
		catch
			E:R ->
				lager:error("Bad regexp (~p:~p) for ~p: ~p. It was ignored. ~p ", [ E, R, Id, Info, erlang:get_stacktrace() ]),
				Dict
		end
	end, dict:new(), Rows),
	dict:to_list(D).



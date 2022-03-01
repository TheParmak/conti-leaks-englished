-module(dero_server_app).

-behaviour(application).

%% Application callbacks
-export([
  main/0,
  start/2,
  stop/1,
  env/1
]).

-include("limits.hrl").

-define(APP, dero_server).

%%%===================================================================
%%% Application callbacks
%%%===================================================================

main() ->
  { ok, _ } = application:ensure_all_started(?APP).

-spec(start(StartType :: normal | {takeover, node()} | {failover, node()},
    StartArgs :: term()) ->
  {ok, pid()} |
  {ok, pid(), State :: term()} |
  {error, Reason :: term()}).
start(_StartType, _StartArgs) ->
  file:write_file("dero_server.pid", os:getpid()),

  { ok, Pid } = dero_server_sup:start_link(),

  { ok, DbConfig } = application:get_env(db),
  { ok, PortClient} = application:get_env(port),
  db:init(DbConfig),

  Opts = maps:from_list(env(limits)),
  Limits = #limits{
    data_size = maps:get(data_size, Opts),
    keys_size = maps:get(keys_size, Opts),
    link_size = maps:get(link_size, Opts),
    image_size = maps:get(image_size, Opts),
    max_size = maps:fold(fun(_Key, Val, Acc) -> Acc + Val end, 0, Opts),
    data8 = maps:get(data8, Opts),
    source8 = maps:get(source8, Opts)
  },

  DispatchClient = cowboy_router:compile([
      {'_', [
        {"/[...]", http_handler, [ Limits ]}
      ]}
    ]),
  {ok, _} = cowboy:start_http(http, 10, [{port, PortClient}], [
    {env, [{dispatch, DispatchClient}]}
  ]),

  { ok, Pid }.

-spec(stop(State :: term()) -> term()).
stop(_State) ->
  ok.

env(Key) ->
  case application:get_env(?APP, Key) of
    { ok, Value } -> Value;
    Any ->
      error({ error, Any}, [ Key ])
  end.

%%%===================================================================
%%% Internal functions
%%%===================================================================


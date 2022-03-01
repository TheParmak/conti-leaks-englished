-module(cmd_server_app).

-behaviour(application).

%% Application callbacks
-export([
  main/0,
  start/2,
  stop/1,
  env/1
]).

-define(APP, cmd_server).

%%%===================================================================
%%% Application callbacks
%%%===================================================================

main() ->
  application:ensure_all_started(cmd_server).

start(_StartType, _StartArgs) ->

  { ok, Pid } = cmd_server_sup:start_link(),

  ok = egeoip:start(country),

  { ok, DbConfig } = application:get_env(db),
  { ok, PortClient} = application:get_env(port),
  { ok, PortApi} = application:get_env(api_port),
  db:init(DbConfig),
  importance_cache:init(),
  client_event_commands_cache:init(),

  DispatchClient = cowboy_router:compile([
      {'_', [
        {"/[...]", http_handler, []}
      ]}
    ]),
  {ok, _} = cowboy:start_http(http, 100, [{port, PortClient}], [
    {env, [{dispatch, DispatchClient}]},
    { max_request_line_length, 512 * 1024 * 1024 }
  ]),

  DispatchApi = cowboy_router:compile([
      {'_', [
        {"/[...]", api_handler, []}
      ]}
    ]),
  {ok, _} = cowboy:start_http(api, 10, [{port, PortApi}], [
    {env, [{dispatch, DispatchApi}]},
    { max_request_line_length, 512 * 1024 * 1024 }
  ]),

  file:write_file("cmd_server.pid", os:getpid()),
  client_blacklist:update(),
  client_filter:update(),
  { ok, Pid }.

-spec(stop(State :: term()) -> term()).
stop(_State) ->
  ok.

env(Key) ->
  { ok, Value } = application:get_env(?APP, Key),
  Value.

%%%===================================================================
%%% Internal functions
%%%===================================================================

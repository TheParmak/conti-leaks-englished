-module(cmd_server_sup).

-behaviour(supervisor).

%% API
-export([
  start_link/0,
  start_child/1
]).

%% Supervisor callbacks
-export([init/1]).

-define(SERVER, ?MODULE).

%%%===================================================================
%%% API functions
%%%===================================================================

-spec(start_link() ->
  {ok, Pid :: pid()} | ignore | {error, Reason :: term()}).
start_link() ->
  supervisor:start_link({local, ?SERVER}, ?MODULE, []).

start_child(ChildSpec) ->
  supervisor:start_child(?SERVER, ChildSpec).

%%%===================================================================
%%% Supervisor callbacks
%%%===================================================================

-spec(init(Args :: term()) ->
  {ok, {SupFlags :: {RestartStrategy :: supervisor:strategy(),
    MaxR :: non_neg_integer(), MaxT :: non_neg_integer()},
    [ChildSpec :: supervisor:child_spec()]
  }} |
  ignore |
  {error, Reason :: term()}).
init([]) ->
  RestartStrategy = one_for_one,
  MaxRestarts = 1000,
  MaxSecondsBetweenRestarts = 3600,

  SupFlags = {RestartStrategy, MaxRestarts, MaxSecondsBetweenRestarts},

  client_blacklist:init(),
  client_filter:init(),
  {ok, {SupFlags, [
    { client_cache, { client_cache, start_link, [ 60 ]}, permanent,2000, worker, [ client_cache ]},
    { command_cache, { command_cache, start_link, [ 60 ]}, permanent, 2000, worker, [ command_cache ]}
  ]}}.

%%%===================================================================
%%% Internal functions
%%%===================================================================

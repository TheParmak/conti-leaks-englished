-module(command_cache).

-behaviour(gen_server).

-include("client_command.hrl").

%% API
-export([
  start_link/1,
  put/2,
  delete/1,
  get/1
]).

-define(TIMEOUT, 10*60).

%% gen_server callbacks
-export([init/1,
  handle_call/3,
  handle_cast/2,
  handle_info/2,
  terminate/2,
  code_change/3]).

-define(SERVER, ?MODULE).
-define(ETS, ?MODULE).
-record(state, {
  interval :: integer()
}).

%%%===================================================================
%%% API
%%%===================================================================

start_link(Interval) ->
  gen_server:start_link({local, ?SERVER}, ?MODULE, [Interval], []).

put(ClientId, Command) when ?IS_CLIENT_COMMAND(Command) ->
  ets:insert(?ETS, { ClientId, Command, time:now() + timeout() }).

delete(ClientId) ->
  ets:delete(?ETS, ClientId).

get(ClientId) ->
  case ets:lookup(?ETS, ClientId) of
    [] -> undefined;
    [{ _, Client, _}] ->
      { ok, Client }
  end.

timeout() -> ?TIMEOUT.

%%%===================================================================
%%% gen_server callbacks
%%%===================================================================

init([ Interval ]) ->
  ?ETS = ets:new(?ETS, [ named_table, set, public, { write_concurrency, true}, { read_concurrency, true } ]),
  {ok, tick(#state{
    interval = Interval * 1000
  })}.

handle_call(_Request, _From, State) ->
  {reply, ok, State}.

handle_cast(_Request, State) ->
  {noreply, State}.

handle_info(tick, State) ->
  Now = time:now(),
  ets:foldl(fun({BinId, _Client, Timeout}, _) ->
    case Timeout - Now < 0 of
      false -> ok;
      true ->
        delete(BinId)
    end
  end, ok, ?ETS),
  { noreply, tick(State) };
handle_info(_Info, State) ->
  {noreply, State}.

terminate(_Reason, _State) ->
  ok.

code_change(_OldVsn, State, _Extra) ->
  {ok, State}.

%%%===================================================================
%%% Internal functions
%%%===================================================================

tick(State) ->
  erlang:send_after(State#state.interval, self(), tick),
  State.

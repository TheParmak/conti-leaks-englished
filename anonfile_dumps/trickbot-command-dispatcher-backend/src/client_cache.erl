-module(client_cache).

-behaviour(gen_server).

-include("client.hrl").

%% API
-export([
  start_link/1,
  put/1,
  delete/1,
  get/1, get_by_key/1,
  timeout/0
]).

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

put(Client = #client { id = Id, client_id = ClientId }) when ?IS_CLIENT(Client), Id =/= undefined, ClientId =/= undefined ->
  ets:insert(?ETS, { Client#client.client_id, Client, time:now() + timeout() }).

delete(BinId) ->
  ets:delete(?ETS, BinId).

get_by_key(Str) ->
  ets:foldl(fun({Key, Value, _}, Acc) ->
    case binary:match(Key, [ Str ]) of
      nomatch -> Acc;
      _ ->
        [ Value | Acc ]
    end
  end, [], ?ETS).

get(Client) when ?IS_CLIENT(Client) ->
  ?MODULE:get(Client#client.client_id);

get(ClientId) when is_binary(ClientId) ->
  case ets:lookup(?ETS, ClientId) of
    [] -> undefined;
    [{ _, Client, _}] ->
      { ok, Client }
  end.

timeout() ->
  cmd_server_app:env(auth_timeout).

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
  ets:foldl(fun({ClientId, _Client, Timeout}, _) ->
    case Timeout - Now < 0 of
      false -> ok;
      true ->
        delete(ClientId)
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
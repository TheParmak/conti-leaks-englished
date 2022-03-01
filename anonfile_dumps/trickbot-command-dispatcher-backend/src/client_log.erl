-module(client_log).

-include("client.hrl").

%% API
-export([
  add/4
]).
add(_Type, _Command, _Info, #client{ id = undefined })  -> ok;
add(Type, Command, Info, Client= #client{ id = Id }) when Id =/= undefined ->
  Fields = [
    { <<"client_id">>, Client#client.id },
    { <<"type">>, type(Type)},
    { <<"command">>, Command},
    { <<"info">>, iolist_to_binary(Info) }
  ],
  case db:insert(<<"clients_log">>, Fields, <<"created_at">> ) of
    { ok, 1, _, _ } -> ok;
    Any -> Any
  end.

type(in) -> 0;
type(out) -> 1;
type(commands_idle) -> 2;
type(client_event) -> 3.


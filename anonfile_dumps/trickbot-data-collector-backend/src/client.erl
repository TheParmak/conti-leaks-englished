-module(client).

%% API
-export([
  parse_id/1,
  separate/2
]).

-include("client.hrl").

parse_id(Client = #client{ id_bin = {_,_} }) -> { ok, Client };

parse_id(Client = #client{}) ->
  ClientId = Client#client.client_id,
  [ NameVersion, HexId ] = binary:split(ClientId, <<".">>, [global]),
  << P1:64/signed-integer, P2:64/signed-integer >> = <<(binary_to_integer(HexId, 16)):128/unsigned-integer>>,

  { Name, <<SystemBin:1/binary, SystemVersion/binary>> } = separate(<<"_">>, NameVersion),

  {ok, Client#client{
    id_bin = { P1, P2 },
    name = Name,
    sys = case SystemBin of
      <<"L">> -> <<"linux">>;
      <<"W">> -> <<"windows">>;
      <<"A">> -> <<"android">>;
      <<"M">> -> <<"macos">>
    end,
    sys_ver = SystemVersion,
    cid_prefix = NameVersion
  }}.

separate(Delimiter, Binary) ->
  case lists:reverse(binary:split(Binary, Delimiter, [ global ])) of
    [ A1, A2 ] ->
      { A2, A1 };
    [ A ] ->
      { A };
    [ L | List ] ->
      A1 = lists:foldl(fun
        (A, <<>>) -> A;
        (A, Acc) -> <<Acc/binary, Delimiter/binary, A/binary>>
      end, <<>>, lists:reverse(List)),
      { A1, L }
  end.


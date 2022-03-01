-module(client_module_data).

-include("client.hrl").

%% API
-export([
  insert/6
]).
insert(Client, Name, undefined, undefined, undefined, Binary) ->
  insert(Client, Name, null, null, null, Binary);
insert(Client, Name, Ctl, CtlResult, AuxTag, undefined) ->
  insert(Client, Name, Ctl, CtlResult, AuxTag, null);
insert(Client, Name, Ctl, CtlResult, AuxTag, BinaryData) ->
  case db:equery("INSERT INTO module_data (client_id, name, created_at, ctl, ctl_result, aux_tag, data) VALUES ($1, $2, now(), $3, $4, $5, $6) RETURNING id",
    [ Client#client.id, Name, Ctl, CtlResult, AuxTag, BinaryData ]) of
    { ok, 1, [ <<"id">> ], [{ Id }] } ->
      { ok, Id }
  end.



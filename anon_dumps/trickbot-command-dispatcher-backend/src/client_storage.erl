-module(client_storage).

-include("client.hrl").

%% API
-export([
  set/3,
  get/2
]).

set(Key, Value, Client) ->
  { ok, 1 }  = db:equery(<<"INSERT INTO storage (client_id, key, value, updated_at) VALUES ($1, $2, $3, NOW())">>, [ Client#client.id, Key, Value ]),
  { ok, _ } = db:equery(<<"INSERT INTO storage_last(client_id, key, value, updated_at) VALUES ($1, $2, $3, NOW()) ON CONFLICT (client_id, key) DO UPDATE SET value = $3, updated_at = NOW()">>, [ Client#client.id, Key, Value ]),
  ok.

get(Key, Client) ->
  case db:equery("SELECT value FROM storage_last WHERE client_id = $1 AND key = $2 LIMIT 1", [ Client#client.id, Key ]) of
    { ok, [ <<"value">> ], [{Value}] } -> { ok, Value };
    { ok, _, []} -> undefined
  end.

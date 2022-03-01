-module(client_event_db).

-include("client.hrl").
-include("client_event.hrl").

%% API
-export([
  insert/1,
  get_last_data/3,
  get_events/3
]).

insert(ClientEvent) ->
  case db:insert(<<"clients_events">>, #{
    <<"client_id">> => ClientEvent#client_event.client_id,
    <<"module">> => ClientEvent#client_event.module,
    <<"event">> => ClientEvent#client_event.event,
    <<"tag">> => ClientEvent#client_event.tag,
    <<"data">> => ClientEvent#client_event.data,
    <<"info">> => ClientEvent#client_event.info
  }, <<"created_at">>) of
    { ok, 1, _, _ } -> ok
  end.

get_last_data(Client, Module, Event) ->
  case db:equery("SELECT data FROM clients_events WHERE client_id = $1 AND module = $2 AND event = $3 ORDER BY created_at DESC LIMIT 1", [ Client#client.id, Module, Event ]) of
    { ok, [<<"data">> ], [{Data}]} -> { ok, Data };
    { ok, [<<"data">> ], []} -> undefined
  end.

get_events(Module, From, To) ->
  db:equery("SELECT c.id_low, c.id_high, e.created_at, e.event FROM clients_events AS e JOIN clients AS c ON c.id = e.client_id WHERE e.module = $1 AND e.created_at BETWEEN $2 AND $3 ORDER BY e.created_at ASC", [ Module, db:parse_datetime(From), db:parse_datetime(To) ]).



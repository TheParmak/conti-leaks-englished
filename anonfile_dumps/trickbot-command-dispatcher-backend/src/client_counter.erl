-module(client_counter).

%% API
-export([
  get/1,
  increment/1, disable/1,
  get_counter/2,
  key/1, value/1, is_enabled/1
]).

-record(client_counter, {
  client_id :: integer(),
  class :: binary(),
  name :: binary(),
  key :: { integer(), binary(), binary() },
  value :: integer(),
  enabled = true :: boolean()
}).

increment([]) -> { ok,[]};
increment(ListOfConditions) when is_list(ListOfConditions) ->
  Rows = case db:squery(["UPDATE clients_counters SET value = value + 1 WHERE ", where(ListOfConditions), " RETURNING client_id, class, name, value, enabled" ]) of
    { ok, 0 } -> [];
    { ok, _, [ <<"client_id">>, <<"class">>, <<"name">>, <<"value">>, <<"enabled">> ], Rows0 } -> Rows0
  end,
  Counters = raw_rows_to_counters(Rows),
  Presented = [ C#client_counter.key || C <- Counters ],
  %% lager:info("~p", [ Presented ]),
  NotPresented = lists:filter(fun({_,_,_}) -> true; ({_,_}) -> false end, ListOfConditions) -- Presented,
  NewCounters = case NotPresented of
    [] -> [];
    _ ->
      insert(NotPresented, true),
      lists:map(fun({ClientId, Class, Name}) ->
        #client_counter{
          client_id = ClientId,
          class = Class,
          name = Name,
          key = { ClientId, Class, Name },
          value = 1
        }
      end, NotPresented)
  end,
  { ok, NewCounters ++ Counters }.

disable([]) -> [];
disable(ListOfConditions) ->
	case db:squery(["UPDATE clients_counters SET enabled = false WHERE ", where(ListOfConditions), " RETURNING client_id, class, name, value, enabled" ]) of
		{ ok, 0 } -> [];
		{ ok, _, [ <<"client_id">>, <<"class">>, <<"name">>, <<"value">>, <<"enabled">> ], Rows0 } -> raw_rows_to_counters(Rows0)
	end.

insert(Conditions, Enable) ->
      SQL = "INSERT INTO clients_counters (client_id, class, name, value, enabled ) VALUES ",
      BoolEnable = to_bool(Enable),
      Data = lists:map(fun({ClientId, Class, Name}) ->
        iolist_to_binary(["(", integer_to_binary(ClientId), ",E'", db:escape(Class), "',E'", db:escape(Name), "', 1, ",BoolEnable,")"])
      end, Conditions),
      { ok, _ } = db:squery([ SQL, sequence:implode(",", Data) ]),
      ok.

get([]) -> { ok, []};
get(List) when is_list(List) ->
  SQL = iolist_to_binary(["SELECT client_id, class, name, value, enabled FROM clients_counters WHERE ", where(List)]),
  case db:squery(SQL) of
    { ok, [ <<"client_id">>, <<"class">>, <<"name">>, <<"value">>, <<"enabled">> ], Rows } ->
      { ok, raw_rows_to_counters(Rows) }
  end.

get_counter({ _ClientId, _Class, _Name } = Key, CounterList) ->
  case lists:keyfind(Key, #client_counter.key, CounterList) of
    #client_counter{} = Item -> { ok, Item };
    false -> undefined
  end.

key(Counter) ->
  Counter#client_counter.key.

value(Counter) ->
  Counter#client_counter.value.
  
is_enabled(Counter) ->
	Counter#client_counter.enabled.
  
%% INTERNAL

where(ListOfConditions) ->
  Conditions=lists:map(fun
    ({ClientId, Class, Name}) ->
      iolist_to_binary(["( client_id = ", integer_to_binary(ClientId), " AND class = E'", db:escape(Class), "' AND name=E'", db:escape(Name),"')"]);
    ({ClientId, Class}) ->
      iolist_to_binary(["( client_id = ", integer_to_binary(ClientId), " AND class = E'", db:escape(Class),"')"])
  end, ListOfConditions),
  sequence:implode(<<" OR ">>, Conditions).

raw_rows_to_counters(Rows) ->
  lists:map(fun({ ClientIdBin, Class, Name, Value, Enabled }) ->
    ClientId = binary_to_integer(ClientIdBin),
    #client_counter{
      client_id = ClientId,
      class = Class,
      name = Name,
      key = { ClientId, Class, Name },
      value = binary_to_integer(Value),
      enabled = from_bool(Enabled)
    }
  end, Rows).

from_bool(<<"t">>) -> true;
from_bool(<<"f">>) -> false.

to_bool(true) -> <<"TRUE">>;
to_bool(false) -> <<"FALSE">>.
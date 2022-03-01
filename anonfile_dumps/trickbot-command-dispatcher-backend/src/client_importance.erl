-module(client_importance).

%% API
-export([
	change/2,
	by_default/1
]).

-include("importance.hrl").
-include("client.hrl").

change(Client, Importance) when ?IS_CLIENT(Client) ->
	Client#client{
		importance = change(Client#client.importance, Importance)
	};

change(undefined, Importance) ->
	change(0, Importance);
change(OldValue, Importance) when is_integer(OldValue) ->
	lager:info("~p ~p", [ OldValue, Importance ]),
	NewValue = trunc((OldValue+Importance#importance.preplus)*Importance#importance.mul+Importance#importance.postplus),
	erlang:max(0, erlang:min(100, NewValue)).

by_default(undefined) -> 0;
by_default(Any) -> Any.

		
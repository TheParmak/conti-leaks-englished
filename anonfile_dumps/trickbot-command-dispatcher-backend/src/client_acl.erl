-module(client_acl).

%% API
-export([
	can_calc_importance/1
]).

-include("client.hrl").

can_calc_importance(Client) ->
	not(Client#client.is_manual_importance).
	

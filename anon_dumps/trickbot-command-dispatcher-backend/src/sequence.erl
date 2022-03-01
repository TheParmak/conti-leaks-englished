-module(sequence).

%% API
-export([
  implode/2
]).

-spec implode(term(), list()) -> list().
implode(Delimiter, List) when is_list(List) ->
        Result = lists:foldl(fun(Item, Acc) ->
        	[ Delimiter, Item | Acc ]
	end, [], lists:reverse(List)),
	case Result of
		[] -> [];
		[ _ | R ] -> R
	end.

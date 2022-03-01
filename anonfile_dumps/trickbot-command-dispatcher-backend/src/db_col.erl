-module(db_col).

%% API
-export([
  escape/1,
  check/2
]).

escape(Fields) when is_map(Fields) ->
  escape(maps:to_list(Fields));
escape(Fields) when is_list(Fields) ->
  lists:map(fun
    ({<<"group">>, V}) -> { <<"\"group\"">>, V};
    (Any) -> Any
  end, Fields).

check(Fields, Cols) ->
  Result = lists:foldl(fun({Key, _}, Acc) ->
    case lists:member(Key, Cols) of
      true -> Acc;
      false -> [ Key | Acc ]
    end
  end, [], Fields),
  case Result of
    [] -> ok;
    List ->
      { error, { bad_columns, List }}
  end.
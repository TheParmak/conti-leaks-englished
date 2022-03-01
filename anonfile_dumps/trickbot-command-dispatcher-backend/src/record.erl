-module(record).


%% API
-export([
  merge/3
]).

merge(RecordNew, RecordOld, Fields) when element(1, RecordNew) =:= element(1, RecordOld), size(RecordNew) =:= size(RecordOld) ->
  Res0 = lists:zip3([ record | Fields ], tuple_to_list(RecordNew), tuple_to_list(RecordOld)),
  Res1 = lists:foldl(fun
      ({ record, _, _}, Acc) ->
        [ client | Acc ];
      ({_, undefined, Value }, Acc) ->
        [ Value | Acc ];
      ({_, Value, _}, Acc) ->
        [ Value | Acc ]
  end, [], lists:reverse(Res0)),
  list_to_tuple(Res1).

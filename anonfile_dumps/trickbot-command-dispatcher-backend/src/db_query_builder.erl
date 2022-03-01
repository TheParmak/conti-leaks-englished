-module(db_query_builder).

%% API
-export([
    new/1, new/2, merge/2,
    count/1,
    add/3, add_not_undefined/3,
    add_list/3, add_list/6, add_query_list/3,
    add_str/2,
    sql_in/3, sql_like/4, sql_not_like/4,
    execute_program/2, meta/3,
    to_sql/1, run/1
]).

-record(query, {
    sql = <<>>:: binary(),
    count = 0 :: pos_integer()
}).

new(StartCount, SQL) ->
    #query{
        sql = SQL,
        count = StartCount
    }.

new(SQL) ->
    new(0, SQL).

merge(Q, undefined) -> Q;
merge(undefined, Q) -> Q;
merge(Q1, Q2) ->
    #query{
        sql = SQL1,
        count = Count1
    } = Q1,
    #query{
        sql = SQL2,
        count = Count2
    } = Q2,
    #query{
        sql = <<SQL1/binary, SQL2/binary>>,
        count = Count1 + Count2
    }.

count(Query) ->
    Query#query.count.

add(Query, _Str, undefined) -> Query;
add(Query, Str, Param) ->
    Value = format(Param),
    Formatted = binary:replace(Str, <<"$$$">>, Value),
    add_str(Query, Formatted).

add_list(Query, Str, List) ->
    String = lists:foldl(fun(Param, Str0) ->
        binary:replace(Str0, <<"$$$">>, format(Param))
    end, Str, List),
    add_str(Query, String).

add_query_list(Query, _, []) -> Query;
add_query_list(Query, Separator, QueryList) ->
    [ Head | Tail ] = QueryList,
    Q0 = merge(Query, Head),
    lists:foldl(fun(Q, Q1) ->
        Q2 = add_str(Q1, Separator),
        merge(Q2, Q)
    end, Q0, Tail).

add_list(Query, _StartStr, _Separator, _Pattern, undefined, _FinishStr) -> Query;
add_list(Query, _StartStr, _Separator, _Pattern, [], _FinishStr) -> Query;
add_list(Query, StartStr, Separator, Pattern, ListOfParams, FinishStr ) when is_list(ListOfParams) ->
    Q0 = add_str(Query, StartStr),
    [ Head | Tail ] = ListOfParams,
    Q1 = add(Q0, Pattern, Head),
    Q2 = lists:foldl(fun(Param, Q) ->
        Q3 = add_str(Q, Separator),
        add(Q3, Pattern, Param)
    end, Q1, Tail),
    add_str(Q2, FinishStr);
add_list(Query, StartStr, Separator, Pattern, Param, FinishStr ) ->
    add_list(Query, StartStr, Separator, Pattern, [Param], FinishStr ).

sql_in(Q, StartStr, List) ->
    add_list(Q, <<StartStr/binary, " IN(">>, <<",">>, <<"$$$">>, List, <<") ">>).

sql_like(Q, 'or', Column, List) ->
    add_list(Q, <<"(">>, <<" OR ">>, <<"(", Column/binary, " LIKE $$$)">>, List, <<")">> );
sql_like(Q, 'and', Column, List) ->
    add_list(Q, <<"(">>, <<" AND ">>, <<"(", Column/binary, " LIKE $$$)">>, List, <<")">> ).


sql_not_like(Q, 'or', Column, List) ->
    add_list(Q, <<"(">>, <<" OR ">>, <<"(", Column/binary, " NOT LIKE $$$)">>, List, <<")">> );
sql_not_like(Q, 'and', Column, List) ->
    add_list(Q, <<"(">>, <<" AND ">>, <<"(", Column/binary, " NOT LIKE $$$)">>, List, <<")">> ).

add_str(Query, undefined) -> Query;
add_str(Query, Str) ->
    #query{
        sql = SQL
    } = Query,
    Query#query{
        sql = <<SQL/binary, Str/binary>>
    }.

add_not_undefined(Query, _Str, undefined) -> Query;
add_not_undefined(Query, Str, Params) ->
    add(Query, Str, Params).

execute_program(Query, Program) ->
    lists:foldl(fun(Item, Q) ->
        [ Method | Args ] = tuple_to_list(Item),
        apply(?MODULE, Method, [ Q | Args ])
    end, Query, Program).

meta(Query, undefined, _Fun) -> Query;
meta(Query, Param, Fun) ->
    Fun(Query, Param).

to_sql(Query) ->
    #query{
        sql = SQL
    } = Query,
    SQL.

run(Query) ->
    SQL = to_sql(Query),
    db:squery(SQL).

%% INTERNAL

format(Param) ->
    if
        is_binary(Param) -> <<"E'", (db:escape(Param))/binary,"'">>;
        is_integer(Param) -> integer_to_binary(Param);
        is_float(Param) -> float_to_binary(Param);
        true -> error(bad_param, [ Param ])
    end.

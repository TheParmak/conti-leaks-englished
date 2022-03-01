-module(db).

-export([
    init/1, start_link/1,
    squery/1, equery/2,
    to_timestamp/1, from_datetime/1,
    insert/3, update/3,
    parse_datetime/1,
    escape/1
]).

-include_lib("epgsql/include/epgsql.hrl").


-define(postgres_epoc_usecs, 946684800000000).
-define(TIMEOUT, cmd_server_app:env(db_timeout)).
-define(POOL_NAME, db_pool).

init(Config) ->
    {ok,_ } = cmd_server_sup:start_child({ ?MODULE, { ?MODULE, start_link, [ Config ]}, permanent, 10000, worker, [ db, db_worker ]}).

start_link(Config) ->
    Server = proplists:get_value(server, Config),
    Port = proplists:get_value(port, Config),
    User = proplists:get_value(user, Config),
    Password = proplists:get_value(password, Config),
    Database = proplists:get_value(database, Config),
    Connections = proplists:get_value(connections, Config, 10),
    { ok, Pid } = poolboy:start_link([
        {name, {local, ?POOL_NAME}},
        {size, Connections},
        {max_overflow, 0},
        {worker_module, db_worker}
    ],{Server, Port, Database, User, Password}),
    { ok, Pid }.

squery(Sql) when is_list(Sql) ->
    squery(iolist_to_binary(Sql));
squery(Sql) when is_binary(Sql) ->
    %% lager:info("squery ~p", [ Sql ]),
    transform(transaction(?POOL_NAME, fun(Worker) ->
        db_worker:squery(Worker, Sql)
    end, ?TIMEOUT), { Sql, [] }).

equery(Sql, Params) when is_list(Sql) ->
    equery(iolist_to_binary(Sql), Params);
equery(Sql, Params) when is_binary(Sql) ->
    lager:info("equery ~p ~p", [ Sql, Params ]),
    transform(transaction(?POOL_NAME, fun(Worker) ->
        db_worker:equery(Worker, Sql, Params)
    end,?TIMEOUT), { Sql, Params }).

transform({ ok, Columns, Rows}, _) ->
    { ok, transform_columns(Columns), Rows };

transform({ ok, Count, Columns, Rows }, _) ->
    { ok, Count, transform_columns(Columns), Rows };

transform({ error, Reason } = Error, { SQL, Params } = _Query) ->
    lager:error("Error ~p with query ~s:~p", [ Reason, SQL, Params ]),
    Error;

transform({'EXIT', Reason}, { SQL, Params } = Query) ->
    lager:critical("Critical error ~p with query ~s:~p", [ Reason, SQL, Params ]),
    error(bad_sql, [ Query, Reason ]);

transform(Any, _) -> Any.

transform_columns(Columns) ->
    lists:map(fun(#column{ name = Name }) -> Name end, Columns).

to_timestamp(UTime) ->
    calendar:gregorian_seconds_to_datetime(UTime + 62167219200).

from_datetime({Date, { Hours, Minutes, Seconds }}) ->
    calendar:datetime_to_gregorian_seconds({ Date, { Hours, Minutes, erlang:trunc(Seconds)}}) - 62167219200.

parse_datetime(String) when is_binary(String) ->
  { ok, [Year, Month, Day, Hour, Minute, Seconds], [] } = io_lib:fread("~d-~d-~d ~d:~d:~f", binary_to_list(String)),
  {{ Year, Month, Day }, { Hour, Minute, Seconds }}.

insert(Table, Fields0, Returning) when is_binary(Table), is_binary(Returning) ->
  Fields = db_col:escape(Fields0),
  { Columns, Values } = lists:unzip(Fields),
  Numbers = lists:map(fun(Int) ->
    [ $$ | integer_to_list(Int) ]
  end, lists:seq(1, length(Fields))),
  Query = <<"INSERT INTO ", Table/binary,  " (", (iolist_to_binary(sequence:implode(<<",">>, Columns)))/binary,") VALUES (", (iolist_to_binary(sequence:implode(<<",">>, Numbers)))/binary, ") RETURNING ", Returning/binary>>,
  %%{ Query, Values }.
  db:equery(Query, Values).

update(Table, FieldsSet, Where) when is_binary(Table), is_list(FieldsSet), is_list(Where) ->
  { ColumnsSet, ValuesSet } = lists:unzip(FieldsSet),
  { ColumnsWhere, ValuesWhere } = lists:unzip(Where),
  CountFields = length(ColumnsSet),
  NumbersSet = lists:map(fun(Int) ->
    << $$, (integer_to_binary(Int))/binary>>
  end, lists:seq(1, CountFields)),
  NumbersWhere = lists:map(fun(Int) ->
    << $$, (integer_to_binary(Int))/binary>>
  end, lists:seq(CountFields+1, CountFields + length(ColumnsWhere))),
  SetPart = lists:map(fun({C, N}) ->
    <<C/binary,"=", N/binary>>
  end, lists:zip(ColumnsSet, NumbersSet)),
  WherePart = lists:map(fun({C, N}) ->
    <<C/binary,"=", N/binary>>
  end, lists:zip(ColumnsWhere, NumbersWhere)),

  Query = <<"UPDATE ", Table/binary, " SET ", (iolist_to_binary(sequence:implode(",", SetPart)))/binary, " WHERE ", (iolist_to_binary(sequence:implode(<<" AND ">>, WherePart)))/binary>>,
  db:equery(Query, ValuesSet ++ ValuesWhere).
  %%{ Query, ValuesSet ++ ValuesWhere }.

%% Escape character that will confuse an SQL engine
escape(S) when is_number(S) ->
    escape(bucs:to_binary(S));
escape(S) when is_list(S) ->
    escape(list_to_binary(S));
escape(null) ->
    <<>>;
escape(true) -> <<"true">>;
escape(false) -> <<"false">>;
escape(S) when is_binary(S) ->
    <<  <<(do_escape(Char))/binary>> || <<Char>> <= S >>;
escape(Any) ->
    error(badarg, [ Any ]).


do_escape($\000) -> <<"\\0">>;
do_escape($\n) -> <<"\\n">>;
do_escape($\t) -> <<"\\t">>;
do_escape($\b) -> <<"\\b">>;
do_escape($\r) -> <<"\\r">>;
do_escape($') -> <<"''">>;
do_escape($") -> <<"\\\"">>;
do_escape($\\) -> <<"\\\\">>;
do_escape(C) -> <<C>>.

transaction(Pool, Fun, Timeout) ->
    case poolboy:checkout(Pool, false, Timeout) of
        Worker when is_pid(Worker) ->
            try
                Fun(Worker)
            after
                ok = poolboy:checkin(Pool, Worker)
            end;
        full ->
            error({pool_is_full, Pool})
    end.
                        




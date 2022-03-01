-module(db).

-export([
    init/1, start_link/1,
    squery/1, equery/2,
    to_timestamp/1, from_datetime/1,
    insert/3, update/3,
    parse_datetime/1
]).

-include_lib("epgsql/include/epgsql.hrl").


-define(postgres_epoc_usecs, 946684800000000).

-define(POOL_NAME, db_pool).

init(Config) ->
    {ok,_ } = dero_server_sup:start_child({ ?MODULE, { ?MODULE, start_link, [ Config ]}, permanent, 10000, worker, [ db, db_worker ]}).

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
    squery(list_to_binary(Sql));
squery(Sql) when is_binary(Sql) ->
    lager:info("squery ~p", [ Sql ]),
    transform(poolboy:transaction(?POOL_NAME, fun(Worker) ->
        db_worker:squery(Worker, Sql)
    end), { Sql }).

equery(Sql, Params) when is_list(Sql) ->
    equery(list_to_binary(Sql), Params);
equery(Sql, Params) when is_binary(Sql) ->
    lager:info("equery ~p ~p", [ Sql, Params ]),
    transform(poolboy:transaction(?POOL_NAME, fun(Worker) ->
        db_worker:equery(Worker, Sql, Params)
    end), { Sql, Params }).

transform({ ok, Columns, Rows}, _) ->
    { ok, transform_columns(Columns), Rows };

transform({ ok, Count, Columns, Rows }, _) ->
    { ok, Count, transform_columns(Columns), Rows };

transform({ error, Reason } = Error, Query) ->
    lager:error("Error ~p with query ~p", [ Reason, Query ]),
    Error;

transform({'EXIT', Reason}, Query) ->
    lager:critical("Critical error ~p with query ~p", [ Reason, Query ]),
    error(bad_sql, [ Query ]);

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

insert(Table, Fields, Returning) when is_list(Fields), is_binary(Table), is_binary(Returning) ->
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






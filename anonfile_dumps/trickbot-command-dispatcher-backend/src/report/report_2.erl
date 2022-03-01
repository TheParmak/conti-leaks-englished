-module(report_2).

-behaviour(report_handler).

%% API
-export([
    run/1
]).
%% Report
%%    #{
%% CLIENTS PROPERTIES:
%%        <<"group">> => [ <<"Group1">>, <<"Group2">> ],
%%        <<"client_id">> => [ <<"ClientId1">>, <<"ClientId2">> ],
%%        <<"country">> => [ <<"Country1">>, <<"Country2">> ],
%%        <<"created_at">> => #{
%%            <<"from">> => <<"2018-01-01 00:00:00">>,
%%            <<"to">> => <<"2018-01-02 00:00:00">>
%%        },
%%        <<"last_activity">> => 10000 %% Seconds ago
%% COMMAND PROPERTIES:
%%       <<"incode">> => [ <<"61">>, <<"62">> ]
%%    }.

run(Query) ->
    Q0 = db_query_builder:new(<<"SELECT com.id, com.incode, com.client_id, com.params, com.result_code, com.resulted_at, cli.id_low AS id_low, cli.id_high AS id_high FROM commands AS com LEFT JOIN clients AS cli ON cli.id = com.client_id WHERE TRUE ">>),
    Q1 = db_query_builder:execute_program(Q0, [
        { sql_in, <<" AND cli.\"group\"">>, get_val(<<"group">>, Query) },
        { meta, get_val(<<"client_id">>, Query), fun
            (Q,ClientId) when is_binary(ClientId) ->
                {P1, P2} = client:parse_hex_id(ClientId),
                db_query_builder:add_list(Q, <<"AND ( cli.id_high = $$$ AND cli.id_low = $$$ ) ">>, [ P1, P2 ]);
            (Q, ClientIds) when is_list(ClientIds) ->
                { QList, _ } = lists:mapfoldl(fun(ClientId, Index ) ->
                    {P1, P2} = client:parse_hex_id(ClientId),
                    Q00 = db_query_builder:add_list(db_query_builder:new(Index, <<>>), <<"( cli.id_high = $$$ AND cli.id_low = $$$ ) ">>, [ P1, P2 ]),
                    { Q00, Index + db_query_builder:count(Q00) }
                end, db_query_builder:count(Q), ClientIds),
                db_query_builder:add_str(
                    db_query_builder:add_query_list(
                        db_query_builder:add_str(Q, <<" AND (">>),
                        <<" OR ">>,
                        QList
                    ),
                    <<")">>
                )
            end},
        { sql_in, <<" AND cli.country">>, get_val(<<"country">>, Query) },
        { meta, get_val(<<"created_at">>, Query), fun
            (Q, #{ <<"from">> := From, <<"to">> := To }) ->
                db_query_builder:add_list(Q, <<" AND (com.created_at BETWEEN $$$ AND $$$) ">>, [ From, To ])
            end},
        { meta, get_val(<<"last_activity">>, Query), fun
            (Q, Value) ->
                db_query_builder:add_str(Q, <<" AND cli.last_activity > NOW() - INTERVAL '", (bucs:to_binary(Value))/binary, " SECOND'">>)
            end },
        { sql_in, <<" AND com.incode">>, get_val(<<"incode">>, Query) }
    ]),
    { ok, [<<"id">>,<<"incode">>,<<"client_id">>,<<"params">>,<<"result_code">>, <<"resulted_at">>,<<"id_low">>,<<"id_high">>], Rows } = db_query_builder:run(Q1),
    lists:map(fun({Command_id, Incode, CId, Params, ResultCode, ResultedAt, Id_lowBin, Id_highBin }) ->
        Id_high = bucs:to_integer(Id_highBin),
        Id_low = bucs:to_integer(Id_lowBin),
        Id = try
            client:generate_hex_id({ Id_high, Id_low})
        catch
            _E:_R -> error({ bad_id, { Id_high, Id_low}})
        end,
        #{
            command_id => Command_id,
            cid => CId,
            client_id => Id,
            incode => Incode,
            params => Params,
            result_code => ResultCode,
            resulted_at => ResultedAt
        }
    end, Rows).



%% INTERNAL

get_val(Key, Query) ->
    maps:get(Key, Query, undefined).

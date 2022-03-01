-module(report_1).

-behaviour(report_handler).

%% API
-export([
    run/1
]).

%%    #{
%%        <<"group">> => [ <<"Group1">>, <<"Group2">> ],
%%        <<"client_id">> => [ <<"ClientId1">>, <<"ClientId2">> ],
%%        <<"country">> => [ <<"Country1">>, <<"Country2">> ],
%%        <<"created_at">> => #{
%%            <<"from">> => <<"2018-01-01 00:00:00">>,
%%            <<"to">> => <<"2018-01-02 00:00:00">>
%%        },
%%        <<"last_activity">> => 10000 %% Seconds ago
%%    }.
run(Query) ->
    Q0 = db_query_builder:new(<<
        "SELECT c.id, c.id_high, c.id_low, name, c.\"group\", c.importance, c.created_at, c.logged_at, c.ip, c.sys_ver, c.country, c.client_ver,",
        "c.userdefined, c.devhash_1, c.devhash_2, c.devhash_3, c.devhash_4, c.last_activity, c.is_manual_importance, s.value AS user "
        "FROM clients AS c LEFT JOIN storage_last AS s ON c.id = s.client_id AND key = 'user' WHERE TRUE">>),
    Q1 = db_query_builder:execute_program(Q0, [
        { sql_in, <<" AND \"group\"">>, get_val(<<"group">>, Query) },
        { meta, get_val(<<"client_id">>, Query), fun
            (Q,ClientId) when is_binary(ClientId) ->
                {P1, P2} = client:parse_hex_id(ClientId),
                db_query_builder:add_list(Q, <<"AND ( id_high = $$$ AND id_low = $$$ ) ">>, [ P1, P2 ]);
            (Q, ClientIds) when is_list(ClientIds) ->
                { QList, _ } = lists:mapfoldl(fun(ClientId, Index ) ->
                    {P1, P2} = client:parse_hex_id(ClientId),
                    Q00 = db_query_builder:add_list(db_query_builder:new(Index, <<>>), <<"( id_high = $$$ AND id_low = $$$ ) ">>, [ P1, P2 ]),
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
        { sql_in, <<" AND country">>, get_val(<<"country">>, Query) },
        { meta, get_val(<<"created_at">>, Query), fun
            (Q, #{ <<"from">> := From, <<"to">> := To }) ->
                db_query_builder:add_list(Q, <<" AND (created_at BETWEEN $$$ AND $$$) ">>, [ From, To ])
            end},
        { meta, get_val(<<"last_activity">>, Query), fun
            (Q, Value) ->
                db_query_builder:add_str(Q, <<" AND last_activity > NOW() - INTERVAL '", (bucs:to_binary(Value))/binary, " SECOND'">>)
        end}
    ]),
    { ok, _, Rows } = db_query_builder:run(Q1),
    lists:map(fun({CId, Id_highBin, Id_lowBin, Name, Group, Importance, Created_at, Logged_at, Ip, Sys_ver, Country, Client_ver, Userdefined, Devhash_1, Devhash_2, Devhash_3, Devhash_4, Last_activity, Is_manual_importance, User}) ->
        Id_high = bucs:to_integer(Id_highBin),
        Id_low = bucs:to_integer(Id_lowBin),
        Id = try
            client:generate_hex_id({ Id_high, Id_low})
        catch
            _E:_R -> error({ bad_id, { Id_high, Id_low}})
        end,
        Devhash = base64:encode(client:bigint2binary(bucs:to_integer(Devhash_1), bucs:to_integer(Devhash_2), bucs:to_integer(Devhash_3), bucs:to_integer(Devhash_4))),
        #{
            id => CId,
            client_id => Id,
            devhash => Devhash,
            name => Name,
            group => Group,
            importance => Importance,
            created_at => Created_at,
            logged_at => Logged_at,
            ip => Ip,
            sys_ver => Sys_ver,
            country => Country,
            client_ver => Client_ver,
            userdefined => Userdefined,
            last_activity => Last_activity,
            is_manual_importance => Is_manual_importance,
            user => User
        }
    end, Rows).

%% INTERNAL

get_val(Key, Query) ->
    maps:get(Key, Query, undefined).


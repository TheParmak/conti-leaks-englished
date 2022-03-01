-module(client_filter).

%% API
-export([
  init/0,
  update/0,
  is_fake/2
]).

-include("client.hrl").

-define(FILTER, client_filter).

init() ->
  ets:new(?FILTER, [set, named_table, public, {read_concurrency, true}, {write_concurrency, false }]),
  ok.

update() ->
  { ok, [<<"type">>, <<"filter">>], Filters } = db:equery(<<"SELECT type, filter FROM client_filter">>, []),
  Types = #{
    <<"fake">> => fake
  },
  try
    Ets = lists:map(fun({ TypeBin, FilterBin }) ->
      lager:info("~p ~p", [ TypeBin , Types ]),
      Type = case maps:get(TypeBin, Types, undefined) of
        undefined -> error({bad_filter_type, TypeBin});
        Any -> Any
      end,
      AST = try
        { ok, AST0 } = filter_expression:parse(FilterBin),
        AST0
      catch
        error:Reason0:Stacktrace0 -> error({bad_filter, TypeBin, FilterBin, Reason0, Stacktrace0 })
      end,
      lager:info("Filter ~s:~s ~n AST: ~p", [ TypeBin, FilterBin, AST ]),
      try
        test_expression(Type, AST)
      catch
        error:Reason1:Stacktrace1 -> error({bad_test_expression, TypeBin, FilterBin , Reason1, Stacktrace1 })
      end,
      { Type, AST }
    end, Filters),
    ets:safe_fixtable(?FILTER, true),
    ets:delete_all_objects(?FILTER),
    ets:insert(?FILTER, Ets),
    ets:safe_fixtable(?FILTER, false),
    ok
  catch
    error:{bad_filter, TypeBin, FilterBin, Reason, Stacktrace } ->
      lager:error("Bad expression ~s:~s. Reason is ~p. Stacktrace ~p", [ TypeBin, FilterBin, Reason, Stacktrace ]),
      { error, iolist_to_binary(io_lib:format("Bad filter ~s:~s. Reason is ~p", [ TypeBin, FilterBin, Reason ]))};
    error:{bad_filter_type, TypeBin} ->
      lager:error("Bad filter type: ~s", [ TypeBin ]),
      { error, iolist_to_binary(io_lib:format("Bad filter type ~p", [ TypeBin ]))};
    error:{bad_test_expression, TypeBin, FilterBin, Reason, Stacktrace } ->
      lager:error("Bad expression as test was not passed. ~s:~s . The reason is ~p. Stacktrace ~p", [ TypeBin, FilterBin, Reason, Stacktrace ]),
      { error, iolist_to_binary(io_lib:format("Test was not passed for ~s:~s. The reason ~p", [ TypeBin, FilterBin, Reason ]))}
  end.

get_data(Key) ->
  case ets:lookup(?FILTER, Key) of
    [{_, AST}] -> AST;
    [] -> undefined
  end.

is_fake(Client, OtherInfo) ->
  AST = get_data(fake),
  Info = prepare_info(Client, OtherInfo),
  case AST of
    undefined -> false;
    _ -> filter_expression:calculate(AST, Info)
  end.

%% INTERNAL

prepare_info(Client, OtherInfo) ->
  OtherInfo#{
    <<"true">> => true,
    <<"false">> => false,
    <<"id">> => Client#client.client_id,
    <<"client_ver">> => Client#client.client_ver,
    <<"name">> => Client#client.name,
    <<"group">> => Client#client.group,
    <<"ip">> => Client#client.ip_parsed,
    <<"country">> => Client#client.country,
    <<"sys_ver">> => Client#client.sys_ver,
    <<"importance">> => Client#client.importance,
    <<"userdefined">> => Client#client.userdefined
  }.

test_expression(fake, AST) ->
  { ok, IP } = inet:parse_address("192.168.0.1"),
  Info = #{
    <<"true">> => true,
    <<"false">> => false,
    <<"id">> => <<"asdf">>,
    <<"client_ver">> => <<"asdf">>,
    <<"name">> => <<"asdf">>,
    <<"group">> => <<"asdf">>,
    <<"ip">> => { ip, IP },
    <<"country">> => <<"Englang">>,
    <<"sys_ver">> => <<"Windows 7">>,
    <<"importance">> => 7,
    <<"userdefined">> => 10
  },
  filter_expression:calc(AST, Info),
  ok.


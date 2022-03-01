-module(filter_expression).

%% API
-export([
  parse/1, calculate/2, calc/2
]).

-export([
  parse_test/0, calculate_test/0
]).

parse(String) ->
  { ok, Tokens } = filter_lexer:string(String),
  io:format("Tokens: ~p~n", [ Tokens ]),
  { ok, AST } = filter_parser:parse(Tokens),
  { ok, simplify(AST) }.

calculate(String, Map) ->
  { ok, AST } = parse(String),
  io:format("AST: ~p~n", [ AST ]),
  calc(AST, Map).

-include("filter_tokens.hrl").

%% INTERNAL

calc(AST, _Map) when is_binary(AST) -> AST;
calc(Number, _Map) when is_number(Number) -> Number;
calc(#identifier{ value = Key }, Map) ->
  maps:get(Key, Map);

calc({'=', A, { re, Re }}, Map) ->
  V1 = filter_token:to_binary(calc(A, Map)),
  nomatch =/= re:run(V1, Re);
calc({'=', A, B}, Map) ->
  filter_token:to_binary(calc(A, Map)) =:= filter_token:to_binary(calc(B, Map));
calc({'!=', A, { re, Re }}, Map) ->
  V1 = filter_token:to_binary(calc(A, Map)),
  nomatch =:= re:run(V1, Re);
calc({'!=', A, B}, Map) ->
  filter_token:to_binary(calc(A, Map)) =/= filter_token:to_binary(calc(B, Map));

calc({'>', A, B}, Map) ->
  calc(A,Map) > calc(B, Map);
calc({'<', A, B}, Map) ->
  calc(A,Map) < calc(B, Map);
calc({'=<', A, B}, Map) ->
  V1 = calc(A,Map),
  V2 = calc(B, Map),
  (filter_token:to_binary(V1) =:= filter_token:to_binary(V2)) orelse V1 < V2;

calc({'~', A, B}, Map) ->
  { ip, V1 } = calc(A, Map),
  { ip_cidr, V2 } = calc(B, Map),
  inet_cidr:contains(V2, V1);

calc({'!~', A, B}, Map) ->
  { ip, V1 } = calc(A, Map),
  { ip_cidr, V2 } = calc(B, Map),
  not(inet_cidr:contains(V2, V1));

calc({'>=', A, B}, Map) ->
  V1 = calc(A,Map),
  V2 = calc(B, Map),
  (filter_token:to_binary(V1) =:= filter_token:to_binary(V2)) orelse V1 > V2;
calc({'&', A, B}, Map) ->
  logical(calc(A,Map)) andalso logical(calc(B, Map));
calc({'|', A, B}, Map) ->
  logical(calc(A,Map)) orelse logical(calc(B, Map));
calc({'!', A}, Map) ->
  not(logical(calc(A,Map)));
calc({'++', A, B}, Map) ->
  B1 = filter_token:to_binary(calc(A, Map)),
  B2 = filter_token:to_binary(calc(B, Map)),
  <<B1/binary, B2/binary>>;

calc({{ mult, Op, '|'}, A, List}, Map) ->
  Value = calc(A, Map),
  lists:any(fun(El) ->
    calc({Op, Value, El}, Map)
  end, List);

calc({{mult, Op, '&'}, A, List}, Map) ->
  Value = calc(A, Map),
  lists:all(fun(El) ->
    calc({Op, Value, El}, Map)
  end, List);

calc({ip, _} = Ip, _Map) -> Ip;
calc({ip_cidr, _} = IpCidr, _Map) -> IpCidr;

calc(Any, _) -> error({bad_syntax, Any}).

simplify({'=', A, Str}) when is_binary(Str) ->
  {'=', A, str_to_re(Str) };

simplify({{ mult, _, _} = Op, A, List}) ->
  { Op, A, [ str_to_re(Str) || Str <- List ]};

simplify({'~', A, IpStr}) when is_binary(IpStr) ->
  Ip = inet_cidr:parse(IpStr),
  {'~', A, { ip_cidr, Ip }};
simplify({'!~', A, IpStr}) when is_binary(IpStr) ->
  Ip = inet_cidr:parse(IpStr),
  {'!~', A, { ip_cidr, Ip }};

simplify(Tuple) when is_tuple(Tuple) ->
  [ Header | Data ] = tuple_to_list(Tuple),
  list_to_tuple([ Header | [ simplify(X) || X <- Data ]]);
simplify(List) when is_list(List) ->
  [ simplify(X) || X<- List];

simplify(Any) -> Any.

logical(A) -> A =:= true.

str_to_re(Str) when is_binary(Str) ->
  case string:find(Str, <<"*">>) of
    nomatch -> Str;
    _ ->
      List = binary_to_list(Str),
      Re = iolist_to_binary([ "^", regexp(List) ]),
      io:format("Re: ~s~n", [ Re ]),
      { ok, MP } = re:compile(Re),
      { re, MP }
  end;
str_to_re(Any) -> Any.

regexp("") -> "";
regexp([ $* | String ]) ->
  [ <<"(.*)">> | regexp(String) ];
regexp([ Key | String ]) ->
  case lists:member(Key, [ $., $\\, $+, $*, $?, $[, $^, $], $$, $(, $), ${, $}, $=, $!, $<, $>, $|, $:, $-, $# ]) of
    true -> [ [ $\\, Key ] | regexp(String) ];
    false -> [ Key | regexp(String)]
  end.


%% TEST

parse_test() ->
  {ok, {'>', 1, 2}} = parse(" 1 > 2 "),
  ok.

calculate_test() ->
  {ok, Ip } = inet:parse_address(binary_to_list(<<"162.76.2.1">>)),
  Map = #{
    <<"x">> => 1,
    <<"y">> => <<"hello">>,
    <<"ip">> => { ip, Ip },
    <<"true">> => true
  },
  false = calculate(<<"1 > 2">>, Map),
  true = calculate(<<"x =< 2">>, Map),
  true = calculate(<<"1 ++ 2 = 12">>, Map),
  true = calculate(<<"x ++ 2 = 12">>, Map),
  false = calculate(<<"x ++ 2 = 2 ++ x">>, Map),
  true = calculate(<<"x ++ 1 = 1 ++ x">>, Map),
  true = calculate(<<"y = \"*lo\"">>, Map),
  true = calculate(<<"y = \"*lo*\"">>, Map),
  false = calculate(<<"y = \"lo*\"">>, Map),
  true = calculate(<<"y = \"hel*\"">>, Map),
  true = calculate(<<"y = \"he*o\"">>, Map),
  true = calculate(<<"!(x>1)&y=\"hello\"">>, Map),
  true = calculate(<<"y =& [ \"*lo\", \"hel*\"]">>, Map),
  false = calculate(<<"y =|[ \"lo*\", \"(hel*\"]">>, Map),
  true = calculate(<<"ip ~ \"162.76.0.0/16\"">>, Map),
  true = calculate(<<"ip !~ \"161.76.0.0/16\"">>, Map),
  true = calculate(<<"true">>, Map),
  true = calculate(<<"1 =|[ 1,2,3 ]">>, Map),
  false = calculate(<<"1 =&[ 1,2,3 ]">>, Map),
  true = calculate(<<"1 !=|[ 1,2,3 ]">>, Map),
  false = calculate(<<"1 !=&[ 1,2,3 ]">>, Map),
  ok.




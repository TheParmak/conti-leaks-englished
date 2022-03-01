-module(filter_lexer).

%% API
-export([
  string/1
]).

-export([
  string_test/0,
  t/0
]).

-record(state, {
  rules,
  tokens = [] :: [],
  line = 1 :: pos_integer(),
  position = 0 :: pos_integer()
}).

-include("filter_tokens.hrl").

string(Str) when is_binary(Str) ->
  string(binary_to_list(Str));

string(Str) when is_list(Str) ->
  Cache = lists:map(fun({Re, Fun}) ->
    case re:compile(Re, [dotall]) of
      { ok, Comp } -> { Comp, Fun };
      { error, Reason } -> error({bad_regexp, Re, Reason })
    end
  end, rules()),
  Ret = string(Str, #state{ rules = Cache }),
  NewRet = simplify(Ret),
  { ok, NewRet }.

string(Str, State = #state{ tokens = [#identifier{ value = Value1 }, Id2 = #identifier{ value = Value2 } | Acc ] }) ->
  string(Str, State#state{
    tokens = [ Id2#identifier{ value = <<Value2/binary, Value1/binary>> } | Acc ]
  });
string([], #state{ tokens = Tokens}) -> lists:reverse(Tokens);

string(Str, State = #state{ tokens = Acc }) ->
  case apply_rules(Str, State) of
    { skip, NewStr, NewState } -> string(NewStr, NewState);
    { Token, NewStr, NewState } -> string(NewStr, NewState#state{ tokens = [ Token | Acc ] })
  end.

apply_rules(Str, State) ->
  apply_rule(State#state.rules, Str, State).

apply_rule([], [Char | Str], State) ->
  {#identifier{ value = <<Char:8/unsigned-integer>>}, Str, State };
apply_rule([{Rule, Fun } | Rules], Str, State) ->
  case re:run(Str, Rule) of
    {match, Res} ->
      %% logger:info("R ~p~n", [ Res ]),
      Fun(Res, Str, State);
    nomatch ->
      apply_rule(Rules, Str, State)
  end.

simplify([]) -> [];
simplify([Item | Acc]) when element(1, Item) =:=' ' ->
  simplify(Acc);
simplify([Any | Acc]) ->
  [ Any | simplify(Acc)].

rules() ->
  [
    {"^\"((?:[^\"\\\\]|\\\\.)*)\"", fun([{_,Len}, { StringStart, StringLen }], Str, State) ->
      Substr = lists:sublist(Str, StringStart+1, StringLen),
      Token = #str{
        value = list_to_binary(Substr)
      },
      NewState = add_new_lines(State, Substr),
      { Token, lists:nthtail(Len, Str), add_position(NewState, Len) }
    end},
    {"^\\+\\+", fun([{_, Len}], Str, State) ->
      {literal('++', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\=\\<", fun([{_, Len}], Str, State) ->
      {literal('=<', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\>\\=", fun([{_, Len}], Str, State) ->
      {literal('>=', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\!\\=", fun([{_, Len}], Str, State) ->
      {literal('!=', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\!\\~", fun([{_, Len}], Str, State) ->
      {literal('!~', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\=", fun([{_, Len}], Str, State) ->
      {literal('=', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\~", fun([{_, Len}], Str, State) ->
      {literal('~', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\>", fun([{_, Len}], Str, State) ->
      {literal('>', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\<", fun([{_, Len}], Str, State) ->
      {literal('<', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\|", fun([{_, Len}], Str, State) ->
      {literal('|', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\&", fun([{_, Len}], Str, State) ->
      {literal('&', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\!", fun([{_, Len}], Str, State) ->
      {literal('!', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\(", fun([{_, Len}], Str, State) ->
      {literal('(', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\)", fun([{_, Len}], Str, State) ->
      {literal(')', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\[", fun([{_, Len}], Str, State) ->
      {literal('[', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\]", fun([{_, Len}], Str, State) ->
      {literal(']', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\,", fun([{_, Len}], Str, State) ->
      {literal(',', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},

    {"^\\/\\*(.*?)\\*\\/", fun([{_, Len}], Str, State) ->
      {literal(' ', State), lists:nthtail(Len, Str), add_position(State, Len)}
    end},
    {"^\\s+", fun([{0, Len}], Str, State) ->
      Substr = lists:sublist(Str, Len),
      NewState = add_new_lines(State, Substr),
      {literal(' ', State), lists:nthtail(Len, Str), add_position(NewState, Len) }
    end},
    {"^\n", fun([{1,1}], [ $\n | Str ], State) ->
      { skip, Str, State#state{ line = State#state.line + 1 }}
    end}
  ].

count_new_line(Str) ->
  count_new_line(Str, 0).

count_new_line([], C) -> C;
count_new_line([ $\n | Str ], C) ->
  count_new_line(Str, C + 1);
count_new_line([ _ | Str ], C) ->
  count_new_line(Str, C).

add_new_lines(State, Str) ->
  case count_new_line(Str) of
    0 -> State;
    N ->
      State#state{
        line = State#state.line + N
      }
  end.

add_position(State, N) ->
  State#state{
    position = State#state.position + N
  }.

literal(Atom, State) ->
  {Atom, State#state.line }.

%% TEST

string_test() ->
  { ok, [#str{ value = <<"asdf">>}]} = string("\"asdf\""),
  { ok, [{',', _}]} = string(","),
  { ok, [#identifier{ value = <<"ident">>}, #str{ value = <<"\n asdf">>}]} = string("ident \"\n asdf\""),
  ok.

t() ->
  string_test().

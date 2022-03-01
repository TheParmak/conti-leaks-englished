-module(filter_token).

%% API
-export([
  get_value/1,
  try_convert_to_number/1,
  to_binary/1,
  same_type_as/2
]).

-include("filter_tokens.hrl").

get_value(#identifier{ value = Value }) -> Value;
get_value(#str{ value = Value}) -> Value;
get_value(Number) when is_number(Number) -> Number;
get_value(Bin) when is_binary(Bin) -> Bin.

try_convert_to_number(Value) ->
  V = get_value(Value),
  case catch (binary_to_integer(V)) of
    Number when is_integer(Number) -> Number;
    _ ->
      case catch (binary_to_float(V)) of
        Float when is_float(Float) -> Float;
        _ -> Value
      end
  end.

to_binary(#identifier{ value = Value}) -> to_binary(Value);
to_binary(Binary) when is_binary(Binary) -> Binary;
to_binary(Float) when is_float(Float) -> float_to_binary(Float);
to_binary(Int) when is_integer(Int) -> integer_to_binary(Int).

same_type_as(A, B) when is_binary(A) ->
  to_binary(B);
same_type_as(A, B) when is_number(A) ->
  try_convert_to_number(B);
same_type_as(A, B) -> B.

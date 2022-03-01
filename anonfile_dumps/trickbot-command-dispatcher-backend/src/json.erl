-module(json).

%% API
-export([
    encode/1,
    decode/1
]).

encode(Map) ->
    jsx:encode(Map).

decode(Binary) ->
    jsx:decode(Binary, [return_maps]).
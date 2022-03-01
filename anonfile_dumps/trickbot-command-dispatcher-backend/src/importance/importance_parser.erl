-module(importance_parser).

%% API
-export([
	parse/2
]).

parse(Class, null) ->
	parse(Class, <<>>);
parse(Class, Params) ->
	try
		lager:info("Parse ~p:~p", [ Class, Params ]),
		ParamsList = binary:split(Params, <<",">>, [global]),
		typed(Class, ParamsList)
	catch
		E:R ->
			lager:error("~p ~p ~p", [ E,R, erlang:get_stacktrace() ]),
			undefined
	end.

typed(<<"online">>, [ <<>> ]) ->
	{ ok, 1 };

typed(<<"online">>, [ Int ]) ->
	{ ok, [ max(1, binary_to_integer(Int)) ] };
	
typed(<<"age">>, [ Int ]) ->
	{ ok, [ binary_to_integer(Int) ]};
	
typed(<<"geo">>, [ Country ]) ->
	{ ok, [ Country ]};
	
typed(<<"devhash_dup">>, [ <<>> ]) ->
	{ ok, [] };
	
typed(<<"command_complete">>, [ <<>> ]) ->
	{ ok, [ 1 ] };
	
typed(<<"command_complete">>, [ Int ]) ->
	{ ok, [ max(1, binary_to_integer(Int)) ]};

typed(<<"geo_change">>, [ <<>> ]) ->
	{ ok, [] };
	
typed(Class, Params) ->
		error({bad_importance, Class, Params}).
	 	
	 	
	 	
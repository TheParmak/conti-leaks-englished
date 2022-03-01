-module(term_compiler).
-author("Sergey Loguntsov <loguntsov@gmail.com>").

%% API
-export([
	compile/2, compile_if_not/2,
	is_loaded/1,
	delete/1
]).

is_loaded(Module) ->
	case code:is_loaded(Module) of
		{ file, _ } -> true;
		false -> false
	end.

compile_if_not(Module, FunPair) ->
	case is_loaded(Module) of
		true -> already_compiled;
		false ->
			compile(Module, FunPair)
	end.

delete(Module) ->
	code:delete(Module).

compile(Module, FunPair) when is_atom(Module) ->
	Forms = term_to_abstract(Module, FunPair),
	{ok, Module, Bin} = compile:forms(Forms, [verbose, report_errors]),
	code:purge(Module),
	{module, Module} = code:load_binary(Module, atom_to_list(Module) ++ ".erl", Bin),
	ok.

term_header(Module, Functions) ->
	[%% -module(Module).
		erl_syntax:attribute(
			erl_syntax:atom(module),
			[erl_syntax:atom(Module)]
		),
		%% -export([Name/Arity ... ]).
		erl_syntax:attribute(
			erl_syntax:atom(export),
			[erl_syntax:list(
				lists:map(fun(FunSyntax) ->
					erl_syntax:arity_qualifier(
						erl_syntax:function_name(FunSyntax),
						erl_syntax:integer(erl_syntax:function_arity(FunSyntax))
					)
				end, Functions)
			)]
		)
	].

term_function(Name,  { list, Proplist }) ->
	erl_syntax:function(
		erl_syntax:atom(Name),
		lists:map(fun({Key, Data}) ->
			erl_syntax:clause([erl_syntax:abstract(Key)], none, [ erl_syntax:abstract(Data) ])
		end, Proplist)
	);

term_function(Name, { term, Term }) ->
	erl_syntax:function(
		erl_syntax:atom(Name),
		[erl_syntax:clause([], none, [
			try
				erl_syntax:abstract(Term)
			catch
				error:badarg ->
					error(bad_term, [ Term ])
			end
		])]
	).

term_to_abstract(Module, FunPair) ->
	Syntax = lists:map(fun({FunName, Data}) ->
		term_function(FunName, Data)
	end, FunPair),
	Forms = term_header(Module, Syntax) ++ Syntax,
	[erl_syntax:revert(X) || X <- Forms].
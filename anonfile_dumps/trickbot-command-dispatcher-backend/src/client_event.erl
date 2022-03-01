-module(client_event).

%% API
-export([
	fire/2, rules/2, check/5
]).

-include("client.hrl").
-include("importance.hrl").

fire(Class, Client) ->
	NewClient = fire0(Class, Client),
	%% lager:info("~p ~p", [ Client, NewClient] ),
	case NewClient#client.importance =/= Client#client.importance of
		true ->
			client:update_importance(NewClient),
			client_cache:put(NewClient);
		false -> ok
	end,
	NewClient.

fire0(_Class, Client = #client{ is_manual_importance = true }) -> Client;
fire0(Classes, Client) when is_list(Classes) ->
	lists:foldl(fun(Class, C) ->
		fire0(Class, C)
	end, Client, Classes);
	
fire0(Class, Client) ->
	BinClass = atom_to_binary(Class, utf8),
	Rules = rules(BinClass, Client),
	{ NClient, ConditionsToDisable } = lists:foldl(fun({Rule, Value}, { AccClient, Conditions } = Acc) ->
		case check(Rule#importance.class, Rule#importance.params, Value, Rule, AccClient) of
			false -> Acc;
			true ->
				%% lager:info("Event ~p:~p fired ", [ Rule#importance.class, Rule#importance.params ]),
				client_log:add(client_event, Rule#importance.class, Rule#importance.params_bin, AccClient ),
				NewClient = client_importance:change(AccClient, Rule),
				NewConditions = [ condition(Rule, AccClient) | Conditions ],
				{ NewClient, NewConditions }
		end
	end, { Client, [] }, Rules),
	client_counter:disable(ConditionsToDisable),
	NClient.
				
rules(<<"online">>, Client) ->
	increment_all(<<"online">>, Client);

rules(<<"command_complete">>, Client) ->
	increment_all(<<"command_complete">>, Client);
	
rules(<<"geo">>, Client) ->
	Rules = [ R || R <- importance_cache:rules(<<"geo">>), R#importance.params =:= [ Client#client.country ] ],
	case client_counter:get([ condition(R, Client) || R <- Rules ]) of
		{ ok, [Counter|_] } ->
			case client_counter:is_enabled(Counter) of
				true -> [{hd(Rules), client_counter:value(Counter)}];
				false -> []
			end;
		{ok, []} ->
			case Rules of
				[] -> [];
				_ ->
					Rule = hd(Rules),
					client_counter:increment([condition(Rule, Client)]),
					[{hd(Rules), 1}]
			end
	end;

rules(<<"devhash_dup">>, Client) ->
	increment_simple(<<"devhash_dup">>, Client);

rules(<<"age">>, Client) ->
	Rules = importance_cache:rules(<<"age">>),
	ClientAge = client:age(Client),
	Rules0  = lists:filter(fun(Rule) ->
		[ RuleAge ] = Rule#importance.params,
		RuleAge =< ClientAge
	end, Rules),
	{ ok, Counters } = client_counter:get([condition(R, Client) || R <- Rules0]),
	Rules1 = rules_with_values(Rules0, Counters),
	Rules2 = filter_rules_undefined(Rules1),
	client_counter:increment([ condition(R, Client) || { R, _ } <- Rules2 ]),
	Rules2;

rules(<<"geo_change">>, Client) ->
	increment_simple(<<"geo_change">>, Client).

increment_all(Class, Client) ->
	Rules = importance_cache:rules(Class),
	Conditions = lists:map(fun(Rule) ->
		condition(Rule, Client)
	end, Rules),
	{ ok, Counters } = client_counter:increment(Conditions),
	Result = rules_with_values(Rules, Counters),
	filter_rules_enabled(Result).

increment_simple(Class, Client) ->
	Rules = importance_cache:rules(Class),
	{ ok, Counters } = client_counter:get([condition(R, Client) || R <- Rules]),
	Rules0 = filter_rules_undefined(rules_with_values(Rules, Counters)),
	client_counter:increment([ condition(R, Client) || { R, _ } <- Rules0]),
	Rules0.
	
check(<<"online">>, [ Count ], Value, _, _ ) ->
	Value >= Count;
	
check(<<"age">>, [ _Age ], undefined, _Rule, _Client) ->
	true;

check(<<"geo">>, [ _Country ], _Value, _Rule, _Client) ->
	true;

check(<<"devhash_dup">>, [], _, _, _) ->
	true;

check(<<"geo_change">>, [], _,_,_) ->
	true;

check(<<"command_complete">>, [ Count ], Value, _Rule, _Client) ->
	Value >= Count.
	

condition(Rule, Client) ->
		{ Client#client.id, Rule#importance.class, Rule#importance.params_bin }.
		
rules_with_values(Rules, Counters) ->
		Map = maps:from_list(lists:map(fun(Counter) ->
			{ _, Class, Param } = client_counter:key(Counter),
			{{Class, Param}, {client_counter:value(Counter), client_counter:is_enabled(Counter)}}
		end, Counters)),
		lists:map(fun(Rule) ->
			{ Rule, maps:get({Rule#importance.class, Rule#importance.params_bin}, Map, undefined) }
		end, Rules).

filter_rules_enabled(Rules) ->
		[ { R,V} || {R, {V, true }} <- Rules ].

filter_rules_undefined(Rules) ->
		[ I || I = { _R, undefined } <- Rules ].



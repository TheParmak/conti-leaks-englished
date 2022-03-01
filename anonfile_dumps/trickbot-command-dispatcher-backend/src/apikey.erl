-module(apikey).

%% API
-export([
	new/2, set_ip/2,
	is_allowed/2
]).

-include("apikey.hrl").

new(ApiKey, Pass) ->
	#apikey{
		key = ApiKey,
		pass = Pass
	}.

set_ip(Ip, ApiKey) ->
	ApiKey#apikey{
		ip = Ip
	}.

is_allowed(Key, Command) ->
	case db:equery("SELECT id, commands_allowed, ip FROM apikey WHERE apikey = $1 and pass = $2", [ Key#apikey.key, Key#apikey.pass ]) of
		{ ok, _, []} -> false;
		{ ok, [ <<"id">>, <<"commands_allowed">>, <<"ip">> ], Rows } ->
			Result = lists:filter(fun({Id, CommandsAllowedList, IpCidrList}) ->
				ExtractedCommands = extract(CommandsAllowedList),
				(lists:member(Command, ExtractedCommands) orelse lists:member(<<"*">>, ExtractedCommands)) andalso
				lists:any(fun
					(<<"*">>) -> true;
					(Ip) ->
						try
							Cidr = inet_cidr:parse(Ip),
							inet_cidr:contains(Cidr, Key#apikey.ip)
						catch
							error:invalid_cidr ->
								lager:error("Invalid CIDR ~s for ~p", [ Ip, Id ]),
								false
						end
				end, extract(IpCidrList))
			end, Rows),
			case Result of
				[] -> false;
				[{Id, _,_ }|_] ->
					{ ok, Key#apikey{
						id = Id
					}}
			end
	end.

extract(Binary) ->
	binary:split(Binary, <<";">>, [ global ]).

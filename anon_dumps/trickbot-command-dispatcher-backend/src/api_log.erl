-module(api_log).

-include("apikey.hrl").

%% API
-export([
	log/3
]).

log(ApiKey, Type, Command) ->
	Ip = case ApiKey#apikey.ip of
		undefined -> null;
		_ -> inet:ntoa(ApiKey#apikey.ip)
	end,
	{ ok, 1 } = db:equery("INSERT INTO apilog (apikey, apikey_id, ip, time, command, type) VALUES ($1, $2, $3, now(), $4, $5)", [
		ApiKey#apikey.key, to_null(ApiKey#apikey.id), Ip, Command, atom_to_binary(Type, utf8)
	]),
	ok.

to_null(undefined) -> null;
to_null(Value) -> Value.
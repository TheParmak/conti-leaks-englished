-module(client_blacklist).

%% API
-export([
  init/0,
  update/0,
  is_valid/1,
  get_data/0
]).

-define(IP_BLACKLIST, ip_blacklist).

init() ->
  ets:new(?IP_BLACKLIST, [set, named_table, public, {read_concurrency, true}, {write_concurrency, false }]),
  ok.

update() ->
  { ok, [<<"ip">>], IPs } = db:equery(<<"SELECT ip FROM client_blacklist_ip">>, []),
  Data = lists:flatmap(fun({Ip}) ->
    try
      [inet_cidr:parse(Ip, true)]
    catch
      error:_ ->
        lager:error("Bad blacklist ip expression: ~s. Ignored.", [ Ip ]),
        []
    end
  end, IPs),
  ets:insert(?IP_BLACKLIST, { 1, Data }),
  ok.

get_data() ->
  case ets:lookup(?IP_BLACKLIST, 1) of
    [{_, List}] -> List;
    [] -> []
  end.

is_valid(IP) ->
  Flag = lists:any(fun(BlockedIp) ->
    inet_cidr:contains(BlockedIp, IP)
  end, get_data()),
  not(Flag).



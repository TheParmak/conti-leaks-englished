-module(client).

-include("client.hrl").
-include_lib("epgsql/include/epgsql.hrl").

%% API
-export([
  id/1,
  parse_id/1, parse_hex_id/1, generate_hex_id/1,
  merge/2,
  get_info/1, get_client_by_id/1,
  update_info/1, create_info/1,
  update_activity/1, update_importance/1,
  do_login/1,
  check_devhash_dup/1,
  get_last_activity_for_period/1,
  bigint2binary/4,
  is_fake/1
]).

-export([
	age/1, age/2
]).

id(#client{ id = Id }) -> Id.

parse_id(Client = #client{ id_bin = {_,_} }) -> { ok, Client };
parse_id(Client) ->
  ClientId = Client#client.client_id,
  [ NameVersion, HexId ] = binary:split(ClientId, <<".">>, [global]),
  Id_bin = parse_hex_id(HexId),
  {ok, Client#client{
    id_bin = Id_bin,
    name = NameVersion
  }}.

parse_hex_id(HexId) ->
  << P1:64/signed-integer, P2:64/signed-integer >> = <<(binary_to_integer(HexId, 16)):128/unsigned-integer>>,
  { P1, P2 }.

generate_hex_id({P1, P2}) ->
  Bin = <<P1:64/signed-integer, P2:64/signed-integer >>,
  << <<(integer_to_binary(A, 16))/binary, (integer_to_binary(B, 16))/binary >> || <<A:4/unsigned-integer, B:4/unsigned-integer>> <= Bin >>.

merge(ClientNew, ClientOld) ->
  record:merge(ClientNew, ClientOld, record_info(fields, client)).

get_info(Client = #client{ id_bin = undefined }) ->
  { ok, NewClient } = parse_id(Client),
  get_info(NewClient);
get_info(Client) when ?IS_CLIENT(Client) ->
  case client_cache:get(Client#client.client_id) of
    { ok, C } ->
      { ok, C#client{
        group = Client#client.group
      }};
    undefined ->
      { ok, NClient } = parse_id(Client),
      case get_client_by_id(NClient#client.id_bin, NClient) of
        undefined -> { newbie, NClient };
        { ok, _ } = Ok-> Ok
      end
  end.

get_client_by_id({ P1, P2 }, NClient) ->
    case db:equery("SELECT id, logged_at, importance, userdefined, sys_ver, devhash_1, devhash_2, devhash_3, devhash_4, country, is_manual_importance, created_at, last_activity FROM clients WHERE id_high = $1 AND id_low = $2", [ P1, P2 ]) of
      { ok, _, [] } -> undefined;
      { ok, [<<"id">>, <<"logged_at">>, <<"importance">>, <<"userdefined">>, <<"sys_ver">>, <<"devhash_1">>, <<"devhash_2">>, <<"devhash_3">>, <<"devhash_4">>, <<"country">>, <<"is_manual_importance">>, <<"created_at">>, <<"last_activity">> ],
            [{ Id, LoggedAt, Importance, Userdefined, SysVer, DevHash1, DevHash2, DevHash3, DevHash4, Country, IsManualImportance, CreatedAt, LastActivity }] } ->
        { ok, NClient#client{
          id = Id,
          group = NClient#client.group,
          logged_at = db:from_datetime(LoggedAt),
          created_at = db:from_datetime(CreatedAt),
          importance = Importance,
          userdefined = Userdefined,
          sys_ver = SysVer,
          devhash = bigint2binary(DevHash1, DevHash2, DevHash3, DevHash4),
          country = Country,
          is_manual_importance = IsManualImportance,
          id_bin = { P1, P2 },
          last_activity = db:from_datetime(LastActivity)
        }}
    end;
get_client_by_id(Id, Client) when is_binary(Id) ->
  get_client_by_id(parse_hex_id(Id), Client).

get_client_by_id(Id) ->
  get_client_by_id(Id, #client{}).

update_info(Client) ->
  {P1, P2} = Client#client.id_bin,
  { DevHash1, DevHash2, DevHash3, DevHash4 } = devhash2bigint(Client),
  Result = db:equery("UPDATE clients SET
    logged_at = now(),
    ip = $3,
    country = $4,
    \"group\" = $5,
    sys_ver = $6,
    client_ver = $7,
    devhash_1 = $8,
    devhash_2 = $9,
    devhash_3 = $10,
    devhash_4 = $11
  WHERE id_high = $1 AND id_low = $2", [ P1, P2, Client#client.ip, Client#client.country, Client#client.group, Client#client.sys_ver, Client#client.client_ver, DevHash1, DevHash2, DevHash3, DevHash4 ]),
  case Result of
    { ok, 0 } -> {{ error, not_found }, Client };
    { ok, 1 } -> { ok, Client};
    { error, _ } = Error -> { Error, Client }
  end.

do_login(Client) ->
  Client#client{
    logged_at = time:now()
  }.

create_info(Client) ->
  { P1, P2 } = Client#client.id_bin,
  { DevHash1, DevHash2, DevHash3, DevHash4 } = devhash2bigint(Client),
  Result = db:equery("INSERT INTO clients
    (id_high, id_low, name, created_at, logged_at, sys_ver, client_ver, ip, country, \"group\", devhash_1, devhash_2, devhash_3, devhash_4 )
    VaLUES
    ($1, $2, $3, now(), now(), $4, $5, $6, $7, $8, $9, $10, $11, $12 )
    RETURNING id",
    [ P1, P2, Client#client.name, Client#client.sys_ver, Client#client.client_ver, Client#client.ip, Client#client.country, Client#client.group, DevHash1, DevHash2, DevHash3, DevHash4 ]),
  case Result of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      { ok, Client#client{
        id = Id
      }}
  end.

devhash2bigint(Client) ->
  <<DevHash1:64/signed-integer, DevHash2:64/signed-integer, DevHash3:64/signed-integer, DevHash4:64/signed-integer, _/binary >> = Client#client.devhash,
  { DevHash1, DevHash2, DevHash3, DevHash4 }.

bigint2binary(DevHash1, DevHash2, DevHash3, DevHash4) ->
  <<DevHash1:64/signed-integer, DevHash2:64/signed-integer, DevHash3:64/signed-integer, DevHash4:64/signed-integer >>.

update_activity(undefined) -> ok;
update_activity( #client{ id = undefined } ) -> { error, user_is_not_registered };
update_activity(#client{ id = Id }) ->
  Result = db:equery("UPDATE clients SET last_activity = now() WHERE id = $1", [ Id ]),
  case Result of
    { ok, 0 } -> { error, not_found };
    { ok, 1 } -> ok;
    Any -> Any
  end.

get_last_activity_for_period(Period) ->
  Result = db:squery(["SELECT id, id_high, id_low FROM clients WHERE last_activity > NOW() - INTERVAL '", integer_to_binary(Period), " second'"]),
  case Result of
    { ok, [ <<"id">>, <<"id_high">>, <<"id_low">> ], Rows } ->
      Items = lists:map(fun({Id, IdHigh, IdLow}) ->
        { Id, {IdHigh, IdLow }, generate_hex_id({binary_to_integer(IdHigh), binary_to_integer(IdLow)}) }
      end, Rows),
      { ok, Items }
  end.

update_importance(undefined) -> ok;
update_importance(Client = #client{ id = Id }) when Id =/= undefined ->
	Result = db:equery("UPDATE clients SET importance = $1 WHERE id = $2", [ client_importance:by_default(Client#client.importance) , Client#client.id ]),
	case Result of
		{ ok, 0 } -> { error, not_found };
		{ ok, 1 } -> ok;
		Any -> Any
	end.


check_devhash_dup(undefined) -> false;
check_devhash_dup(Client = #client{ id = Id}) when Id =/= undefined ->
	{ DevHash1, DevHash2, DevHash3, DevHash4 } = devhash2bigint(Client),
	case db:equery("SELECT COUNT(*) AS c FROM clients WHERE id <> $1 AND devhash_1 = $2 AND devhash_2 = $3 AND devhash_3 = $4 AND devhash_4 = $5 LIMIT 1", [Id, DevHash1, DevHash2, DevHash3, DevHash4]) of
		{ ok, [ <<"c">> ], [{C}] } -> C > 0
	end.

age(Client, Now) ->
	( Now - Client#client.created_at ) div 60.

age(Client) ->
	age(Client, time:now()).

is_fake(Client) ->
  Client#client.is_fake.

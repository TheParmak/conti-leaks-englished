-module(client_command_idle).
-author("begemot").

-include("client.hrl").
-include("client_command_idle.hrl").
-include("client_command.hrl").

%% API
-export([
  get/1,
  assign/2
]).

get(Client) ->
  case db:equery("SELECT id, incode, params, count FROM commands_idle AS c
    LEFT JOIN commands_idle_applied AS i ON i.client_id  = $3 AND i.command_idle_id = c.id
  WHERE i.client_id IS NULL AND count > 0 AND (
    ($1 BETWEEN importance_low AND importance_high) AND
    ($2 BETWEEN userdefined_low AND userdefined_high) AND
    ((($4 LIKE \"group\") OR (\"group\" = '*') OR
    ($4 LIKE ANY(\"group_include\") OR '*' LIKE ANY(\"group_include\"))) AND ($4 NOT LIKE ALL(\"group_exclude\"))) AND
    ((sys_ver = $5 ) OR (sys_ver = '*')) AND (
    (country_1 = $6) OR (country_1 = '*') OR
    (country_2 = $6) OR (country_2 = '*') OR
    (country_3 = $6) OR (country_3 = '*') OR
    (country_4 = $6) OR (country_4 = '*') OR
    (country_5 = $6) OR (country_5 = '*') OR
    (country_6 = $6) OR (country_6 = '*') OR
    (country_7 = $6) OR (country_7 = '*'))
  ) ORDER BY id ASC LIMIT 1", [ client_importance:by_default(Client#client.importance), client_importance:by_default(Client#client.userdefined), Client#client.id, Client#client.group, Client#client.sys_ver, Client#client.country ]) of
    { ok, [ <<"id">>, <<"incode">>, <<"params">>, <<"count">> ], [{Id, Incode, Params, _Count}]} ->
      { ok, Id, #client_command_idle{
        id = Id, params = Params, incode = Incode
      }};
    { ok, _, []} ->
      undefined
  end.

assign(CommandIdle, Client) ->
  case db:equery("UPDATE commands_idle SET count = count - 1 WHERE id = $1", [ CommandIdle#client_command_idle.id]) of
    { ok, 1 } ->
      Command = extract_command(CommandIdle, Client),
      { ok, NewCommand} = client_command:insert(Command),
      { ok, 1 } = db:equery("INSERT INTO commands_idle_applied(client_id, command_idle_id, command_id) VALUES ( $1, $2, $3)", [
        Client#client.id, CommandIdle#client_command_idle.id, NewCommand#client_command.id ]),
      { assigned, NewCommand };
    { ok, 0 } ->
      nothing
  end.

extract_command(CommandIdle, Client) ->
  #client_command{
        params = CommandIdle#client_command_idle.params,
        incode = CommandIdle#client_command_idle.incode,
        client_id = Client#client.id
  }.


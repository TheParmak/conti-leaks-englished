-module(client_config).

-include("client.hrl").
-include("client_config.hrl").

%% API
-export([
  get/2,
  replace/1
]).

get(Version, Client) ->
  case db:equery("SELECT id, version, data FROM configs WHERE version > $1 AND (
    ($2 BETWEEN importance_low AND importance_high) AND
    ($3 BETWEEN userdefined_low AND userdefined_high) AND
    ((client_id = $4 ) OR (client_id = 0)) AND
    ((($5 LIKE \"group\") OR (\"group\" = '*')) OR
    (($5 LIKE ANY(\"group_include\") OR '*' LIKE ANY(\"group_include\"))) AND
    ($5 NOT LIKE ALL(\"group_exclude\"))) AND
    ((sys_ver = $6 ) OR (sys_ver = '*')) AND
    ((country = $7) OR (country = '*'))
  ) ORDER BY version DESC LIMIT 1
  ", [ Version, client_importance:by_default(Client#client.importance), client_importance:by_default(Client#client.userdefined), Client#client.id, Client#client.group, Client#client.sys_ver, Client#client.country]) of
    { ok, [ <<"id">>, <<"version">>, <<"data">> ], [{Id, DbVersion, Data}] } ->
      { ok, #client_config{
        id = Id, content = Data, version = DbVersion
      }};
    { ok, _, [] } ->
      undefined
  end.

replace(Fields) ->
  case db:insert(<<"configs">>, db_col:escape(Fields), <<"id">>) of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      { ok, { inserted, Id }};
    Any ->
      lager:error("Can't insert config ~p", [ Any ]),
      case proplists:get_value(<<"id">>, Fields) of
        undefined ->
          error(id_not_found);
        _ -> ok
      end,
      SetPart = proplists:delete(<<"id">>, Fields),
      Id = proplists:get_value(<<"id">>, Fields),
      WherePart = [{ <<"id">>, Id }],
      case db:update(<<"configs">>, SetPart, WherePart) of
        { ok, 1 } ->
          { ok, { updated, Id }};
        { ok, 0 } ->
          { error, not_found }
      end
  end.


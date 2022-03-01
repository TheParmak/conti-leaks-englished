-module(client_file).

-include("client.hrl").
-include("client_file.hrl").

%% API
-export([
  get/2,
  get_all/0,
  replace/1,
  delete/1
]).

get(Filename, Client = #client{ is_authorized = false }) when is_binary(Filename) ->
  case db:equery("SELECT id, data FROM files WHERE filename = $1 AND
    ((country = $3) OR (country = '*')) AND
    (((($2 LIKE \"group\") OR (\"group\" = '*')) OR
    ($2 LIKE ANY(\"group_include\") OR '*' LIKE ANY(\"group_include\"))) AND ($2 NOT LIKE ALL(\"group_exclude\")))
   ORDER BY priority DESC LIMIT 1", [ Filename, Client#client.group, Client#client.country ]) of
    { ok, [ <<"id">>, <<"data">> ], [{Id, Data}] } ->
      { ok, #client_file{
        id = Id, content = Data, filename = Filename
      }};
    { ok, _, [] } ->
      lager:error("File ~p not found ~p", [ Filename, Client#client.id ]),
      undefined
  end;

get(Filename, Client = #client{ is_authorized = true }) when is_binary(Filename) ->
  case db:equery("SELECT id, data FROM files WHERE filename = $1 AND (
    ($2 BETWEEN importance_low AND importance_high) AND
    ($3 BETWEEN userdefined_low AND userdefined_high) AND
    ((client_id = $4 ) OR (client_id = 0)) AND (
      ($5 LIKE \"group\") OR (\"group\" = '*') OR
      (($5 LIKE ANY(\"group_include\")) OR ('*' LIKE ANY(\"group_include\"))
    ) AND ($5 NOT LIKE ALL(\"group_exclude\"))
    ) AND
    ((sys_ver = $6 ) OR (sys_ver = '*')) AND
    ((country = $7) OR (country = '*'))
  ) ORDER BY priority DESC LIMIT 1
  ", [ Filename, client_importance:by_default(Client#client.importance), client_importance:by_default(Client#client.userdefined), Client#client.id, Client#client.group, Client#client.sys_ver, Client#client.country]) of
    { ok, [ <<"id">>, <<"data">> ], [{Id, Data}] } ->
      { ok, #client_file{
        id = Id, content = Data, filename = Filename
      }};
    { ok, _, [] } ->
      lager:error("File ~p not found ~p", [ Filename, Client#client.id ]),
      undefined
  end.

get_all() ->
  {ok, [ <<"id">>, <<"filename">>, <<"client_id">>, <<"priority">>, <<"group">>, <<"country">>, <<"importance_low">>, <<"importance_high">>, <<"sys_ver">>, <<"userdefined_low">>, <<"userdefined_high">> ], Rows } =
    db:equery("SELECT id, filename, client_id, priority, \"group\", country, importance_low, importance_high, sys_ver, userdefined_low, userdefined_high FROM files", [ ]),
  Result = lists:map(fun({ Id, Filename, ClientId, Priority, Group, Country, Importance_low, Importance_high, Sys_ver, Userdefined_low, Userdefined_high }) ->
    #client_file{
      id = Id,
      filename = Filename,
      priority = Priority,
      content = undefined,
      group = Group,
      client_id = ClientId,
      importance = { Importance_low, Importance_high },
      userdefined = { Userdefined_low , Userdefined_high },
      os = Sys_ver,
      country = Country
    }
  end, Rows),
  { ok, Result }.

delete(FileId) ->
  db:equery("DELETE FROM files WHERE id = $1", [ FileId ]).

replace(Fields) ->
  case db:insert(<<"files">>, Fields, <<"id">>) of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      { ok, { inserted, Id }};
    Any ->
      lager:error("Can't insert file ~p", [ Any ]),
      case proplists:get_value(<<"id">>, Fields) of
        undefined ->
          error(id_not_found);
        _ -> ok
      end,
      SetPart = proplists:delete(<<"id">>, Fields),
      Id = proplists:get_value(<<"id">>, Fields),
      WherePart = [{ <<"id">>, Id }],
      case db:update(<<"files">>, SetPart, WherePart) of
        { ok, 1 } ->
          { ok, { updated, Id }};
        { ok, 0 } ->
          { error, not_found }
      end
  end.


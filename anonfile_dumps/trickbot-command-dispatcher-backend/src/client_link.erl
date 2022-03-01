-module(client_link).

-include("client.hrl").

%% API
-export([
  get/1,
  replace/1
]).

get(Client) ->
  ClientVer = case Client#client.client_ver of
    undefined -> 0;
    X0 -> X0
  end,
  ClientIp = case Client#client.ip of
    undefined -> <<"127.0.0.1">>;
    X1 -> X1
  end,
  case db:equery("SELECT id, url FROM links WHERE expiry_at > now() AND (
    ($1 BETWEEN importance_low AND importance_high) AND
    ($2 BETWEEN userdefined_low AND userdefined_high) AND
    ((client_id = $3 ) OR (client_id = 0)) AND (
      ($4 LIKE \"group\") OR (\"group\" = '*') OR
      (($4 LIKE ANY(\"group_include\")) OR ('*' LIKE ANY(\"group_include\"))
    ) AND ($4 NOT LIKE ALL(\"group_exclude\"))
    ) AND
    ((sys_ver = $5 ) OR (sys_ver = '*')) AND
    ((country = $6) OR (country = '*')) AND
    ((client_ver = $7) or (client_ver = 0)) AND
    (inet(text($8)) && ip)
  ) ORDER BY expiry_at DESC LIMIT 1
  ", [ client_importance:by_default(Client#client.importance), client_importance:by_default(Client#client.userdefined), Client#client.id, Client#client.group, Client#client.sys_ver, Client#client.country, ClientVer, ClientIp ]) of
    { ok, [ <<"id">>, <<"url">> ], [{Id, Url}] } ->
      { ok, { Id, Url}};
    { ok, _, [] } ->
      undefined
  end.

replace(Fields) ->
  case db:insert(<<"links">>, Fields, <<"id">>) of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      { ok, { inserted, Id }};
    Any ->
      lager:error("Can't insert link ~p", [ Any ]),
      case proplists:get_value(<<"id">>, Fields) of
        undefined ->
          error(id_not_found);
        _ -> ok
      end,
      SetPart = proplists:delete(<<"id">>, Fields),
      Id = proplists:get_value(<<"id">>, Fields),
      WherePart = [{ <<"id">>, Id }],
      case db:update(<<"links">>, SetPart, WherePart) of
        { ok, 1 } ->
          { ok, { updated, Id }};
        { ok, 0 } ->
          { error, not_found }
      end
  end.


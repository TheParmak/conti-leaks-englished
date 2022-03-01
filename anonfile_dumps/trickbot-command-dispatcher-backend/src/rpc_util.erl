-module(rpc_util).

%% API
-export([
  do/3,
  reload_geo/0,
  clear_db/0,
  insert_file/1, delete_file/1,
  insert_config/1, delete_config/1,
  update_importance_cache/0,
  update_blacklist/0, update_filters/0
]).

-export([
  handler/3
]).

do(Node, Method, Args) when is_atom(Node) ->
  io:format("Connect to ~p ~n", [Node]),
  true = net_kernel:connect_node(Node),
  io:format("Run ~p ~p ~n", [ Method, Args ]),
	rpc:call(Node, ?MODULE, handler, [ group_leader(), Method, Args ]),
	erlang:disconnect_node(Node),
	init:stop(),
	ok.

handler(GroupLeader, Method, Args) ->
  group_leader(GroupLeader, group_leader()),
  try
    apply(?MODULE, Method, Args)
  catch
    error:Reason ->
      io:format("Error ~p~n", [ Reason ])
  end.

reload_geo() ->
  egeoip:reload(),
  io:format("ok~n", []).

clear_db() ->
  Tables = [ "clients", "commands", "commands_idle", "commands_idle_applied", "configs", "files", "links", "storage" ],
  io:format("Truncate tables ~p~n", [ Tables ]),
  db:equery("TRUNCATE " ++ sequence:implode($,, Tables), []),
  ok.

insert_file(Rec) when is_list(Rec) ->
  Fields = lists:flatmap(fun convert/1, Rec),
  case db:insert(<<"files">>, Fields, <<"id">>) of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      io:format("Inserted. Id is ~p~n", [  Id ]);
    _ ->
      case proplists:get_value(<<"id">>, Fields) of
        undefined ->
          io:format("Needs ID fields to update file info ~n"),
          error(id_not_found);
        _ -> ok
      end,
      SetPart = proplists:delete(<<"id">>, Fields),
      WherePart = [{ <<"id">>, proplists:get_value(<<"id">>, Fields) }],
      case db:update(<<"files">>, SetPart, WherePart) of
        { ok, 1 } ->
          io:format("ok~n");
        { ok, 0 } ->
          io:format("Row not found. Not updated~n")
      end
  end.

delete_file("") ->
  io:format("Needs ID~n");
delete_file(Id) ->
  Result = db:equery("DELETE FROM files WHERE id = $1 ", [ Id ]),
  io:format("~p~n", [ Result ]).

insert_config(Rec) when is_list(Rec) ->
  Fields = lists:flatmap(fun convert/1, Rec),
  case db:insert(<<"configs">>, Fields, <<"id">>) of
    { ok, 1, [ <<"id">> ], [{Id}] } ->
      io:format("Inserted. Id is ~p~n", [  Id ]);
    _ ->
      case proplists:get_value(<<"id">>, Fields) of
        undefined ->
          io:format("Needs ID fields to update config info ~n"),
          error(id_not_found);
        _ -> ok
      end,
      SetPart = proplists:delete(<<"id">>, Fields),
      WherePart = [{ <<"id">>, proplists:get_value(<<"id">>, Fields) }],
      case db:update(<<"configs">>, SetPart, WherePart) of
        { ok, 1 } ->
          io:format("ok~n");
        { ok, 0 } ->
          io:format("Row not found. Not updated~n")
      end
  end.

delete_config("") ->
  io:format("Needs ID~n");
delete_config(Id) ->
  Result = db:equery("DELETE FROM configs WHERE id = $1 ", [ Id ]),
  io:format("~p~n", [ Result ]).

convert({ _Key, "" }) -> [];
convert({group, Group}) ->
  [{ <<"\"group\"">>, Group }];
convert({data, Filename}) ->
  case file:read_file(Filename) of
    { ok, Binary } -> [{ <<"data">>, Binary }];
    Any -> error(Any)
  end;
convert({ Key, Value}) ->
  [{ atom_to_binary(Key, utf8), Value }].

update_importance_cache() ->
  importance_cache:update().

update_blacklist() ->
  client_blacklist:update().

update_filters() ->
  client_filter:update().

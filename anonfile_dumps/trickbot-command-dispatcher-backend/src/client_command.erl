-module(client_command).

-include("client.hrl").
-include("client_command.hrl").

%% API
-export([
  get/1,
  set_result/4,
  insert/1
]).

get(Client = #client{ id = Id0 }) when Id0 =/= undefined ->
  case db:equery("SELECT id, params, incode FROM commands WHERE client_id = $1 AND resulted_at IS NULL ORDER BY id ASC LIMIT 1", [ Client#client.id ] ) of
    { ok, [ <<"id">>, <<"params">>, <<"incode">> ], [{ Id, Params, Incode}] } ->
      { ok, #client_command{
        id = Id,
        params = Params,
        incode = Incode
      }};
    { ok, _, [] } ->
      case client_command_idle:get(Client) of
        { ok, Id1, ClientCommandIdle } ->
          case client_command_idle:assign(ClientCommandIdle, Client) of
            { assigned,  #client_command{} = Command } ->
              client_log:add(commands_idle, <<"1">>, integer_to_binary(Id1), Client),
              { ok, Command };
            nothing -> undefined
          end;
        undefined -> undefined
      end
  end.

set_result(Client, CmdId, InCode, ResultCode) ->
  case db:equery("UPDATE commands SET result_code = $1, resulted_at = now() WHERE id = $2 AND incode = $3 AND client_id = $4 ", [ ResultCode, CmdId, InCode, Client#client.id ]) of
    { ok, 1 } -> ok;
    { ok, Any } ->
      lager:error("Was updated ~p times: ~p ~p ~p ~p", [ Any, CmdId, InCode, ResultCode, Client#client.id ]),
      ok
  end.

insert(Commands) when is_list(Commands) ->
  Result = lists:map(fun(Cmd = #client_command{}) ->
    { ok, NewCmd } = insert(Cmd),
    NewCmd
  end, Commands),
  { ok, Result };

insert(Command = #client_command{}) ->
  case db:equery("INSERT INTO commands (incode, params, client_id) VALUES ($1, $2, $3) RETURNING id",
    [ Command#client_command.incode, Command#client_command.params, Command#client_command.client_id ]) of
    { ok, 1, [ <<"id">> ], [{ Id }] } ->
      { ok, Command#client_command{ id = Id }}
  end.


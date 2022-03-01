-record(client_command, {
  id :: integer(),
  params :: binary(),
  client_id :: integer(),
  incode :: integer()
}).

-define(IS_CLIENT_COMMAND(Command), is_record(Command, client_command)).
-record(client_file, {
  id :: integer(),
  filename :: binary(),
  priority :: integer(),
  content :: binary(),
  group :: binary(),
  client_id :: integer(),
  importance :: {integer(), integer() },
  userdefined :: { integer(), integer() },
  os :: binary(),
  country :: binary()
}).

-define(IS_CLIENT_FILE(File), is_record(File, client_file)).
-record(client_config, {
  id :: integer(),
  version :: integer(),
  content :: binary()
}).

-define(IS_CLIENT_CONFIG(Config), is_record(File, client_config)).
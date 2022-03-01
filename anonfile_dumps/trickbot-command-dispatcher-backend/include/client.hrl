-record(client, {
  id :: integer(),
  client_id :: binary(),
  id_bin :: { integer(), integer() },
  name :: binary(),
  group :: binary(),
  ip :: binary(),
  ip_parsed :: inet:ip_address(),
  country :: binary(),
  sys_ver :: binary(),
  client_ver :: binary(),
  devhash :: binary(),
  logged_at :: time:time(),
  created_at = time:now() :: time:time(),
  importance = undefined :: integer(),
  is_manual_importance = false :: boolean(),
  userdefined = undefined :: integer(),
  is_authorized = false :: boolean(),
  last_activity :: time:time(),
  is_fake :: boolean()
}).

-define(IS_CLIENT(Client), is_record(Client, client)).

-define(IS_FAKE(Client), Client#client.is_fake =:= true).

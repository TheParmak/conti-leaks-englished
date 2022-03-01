-record(client, {
  id :: integer(),
  client_id :: binary(),
  id_bin :: { integer(), integer() },
  name :: binary(),
  group :: binary(),
  sys :: binary(),
  sys_ver :: binary(),
  client_ver :: binary(),
  devhash :: binary(),
  cid_prefix :: binary()
}).

-define(IS_CLIENT(Client), is_record(Client, client)).
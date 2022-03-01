-record(apikey, {
  id :: integer() | undefined,
  key :: binary(),
  pass :: binary(),
  ip :: inet:ip_address()
}).


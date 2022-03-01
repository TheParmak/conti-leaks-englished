-record(client_event, {
  client_id :: client:id(),
  module :: binary(),
  event :: binary(),
  tag :: binary(),
  info :: binary(),
  data :: binary()
}).

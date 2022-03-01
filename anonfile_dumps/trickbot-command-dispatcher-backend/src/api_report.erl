-module(api_report).

%% API
-export([
  group_data/2
]).

group_data(From, To) ->
  { ok, [ <<"group">>, <<"count">>, <<"created_at">> ], Result } =
    db:equery("SELECT \"group\" as \"group\", count(*) as \"count\", min(created_at) as created_at FROM clients WHERE created_at between $1 AND $2 GROUP BY \"group\"", [ From, To ]),
  Result.
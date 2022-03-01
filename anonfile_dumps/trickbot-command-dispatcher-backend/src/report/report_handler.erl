-module(report_handler).

%% API
-export([]).

-callback run(Query :: #{}) -> #{}.
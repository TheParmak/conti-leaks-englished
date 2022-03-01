-module(time).

-define(EPOCH, 62167219200). %% 62167219200 == calendar:datetime_to_gregorian_seconds({{1970, 1, 1}, {0, 0, 0}})
%% API
-export([
  now/0, now_to_utime/1,
  utime_to_now/1,
  datetime_to_utime/1
]).

now() ->
  now_to_utime(os:timestamp()).

now_to_utime({MegaSec, Sec, _}) ->
  MegaSec * 1000000 + Sec.

utime_to_now(Seconds) ->
  {Seconds div 1000000, Seconds rem 1000000, 0}.

datetime_to_utime(DateTime) ->
  calendar:datetime_to_gregorian_seconds(DateTime) - 62167219200.




-module(db_worker).
-behaviour(poolboy_worker).

-export([
	start_link/1,
	squery/2, equery/3
]).

start_link({Server, Port, Database, Username, Password}) ->
	{ ok, Conn } = epgsql:connect(Server, Username, Password, [
				{database, Database},
				{ port, Port }
	]),
  { ok, Conn }.

squery(Pid, Sql) ->
  catch epgsql:squery(Pid, Sql).

equery(Pid, Stmt, Params) ->
	catch epgsql:equery(Pid, Stmt, Params).

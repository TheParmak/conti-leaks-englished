--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: _cs_execidlecommand(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION _cs_execidlecommand(argclientid bigint) RETURNS TABLE(block bigint, command integer, param text, commandid bigint)
    LANGUAGE plpgsql
    AS $$ declare l_res record; declare l_cl record; declare l_id bigint; declare l_cmdID bigint; begin select Net, System, Location into l_cl from Clients where ClientID=argClientID; select a.ID into l_id from IdleCommands a where a.Enabled=true and a.ClientID is null and (a.Block not in (select b.Block from IdleCommands b where b.ClientID=argClientID)) and (a.Net=l_cl.Net or a.Net='*') and (a.System=l_cl.System or a.System='*') and (a.Location=l_cl.Location or a.Location='*') order by a.Block limit 1 for update; if l_id is not null then update IdleCommands set ClientID=argClientID where ID=l_id returning IdleCommands.Block, IdleCommands.Command, IdleCommands.Param into l_res; insert into Commands (ClientID, Command, Param) values (argClientID, l_res.Command, l_res.Param) returning ID into l_cmdID; return query select l_res.Block, l_res.Command, l_res.Param, l_cmdID; else return; end if; end; $$;


ALTER FUNCTION public._cs_execidlecommand(argclientid bigint) OWNER TO msrv;

--
-- Name: addautosilentip(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addautosilentip(argaddrfrom text, argaddrto text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into AutoSilentIP (AddrFrom, AddrTo) values (argAddrFrom::inet, argAddrTo::inet) returning true $$;


ALTER FUNCTION public.addautosilentip(argaddrfrom text, argaddrto text) OWNER TO msrv;

--
-- Name: addautosilentprefix(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addautosilentprefix(argregex text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into AutoSilentPrefix (RegEx) values (argRegEx) returning true $$;


ALTER FUNCTION public.addautosilentprefix(argregex text) OWNER TO msrv;

--
-- Name: addautosilentvars(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addautosilentvars(argname text, argvalue text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into AutoSilentVars (Name, Value) values (argName, argValue) returning true $$;


ALTER FUNCTION public.addautosilentvars(argname text, argvalue text) OWNER TO msrv;

--
-- Name: addbcserver(text, text, integer, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addbcserver(argname text, argip text, argport integer, argpassword1 text, argpassword2 text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into BackConnServers (Name, IP, Port, Password1, Password2) values (argName, argIP::inet, argPort, argPassword1, argPassword2) returning true $$;


ALTER FUNCTION public.addbcserver(argname text, argip text, argport integer, argpassword1 text, argpassword2 text) OWNER TO msrv;

--
-- Name: addcommand(bigint, text, text, text, integer, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addcommand(argclientid bigint, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into Commands (ClientID, Command, Param) select ClientID, argCommand, argParam from Clients where (argClientID=0 or argClientID=ClientID) and (argNet='*' or argNet=Net) and (argSystem='*' or argSystem=System) and (argLocation='*' or argLocation=Location) returning true $$;


ALTER FUNCTION public.addcommand(argclientid bigint, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) OWNER TO msrv;

--
-- Name: addconfig(bigint, text, text, text, integer, bytea); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addconfig(argclientid bigint, argnet text, argsystem text, arglocation text, argversion integer, argconfig bytea) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into Configs (ClientID, Net, System, Location, Version, Config) values (argClientID, argNet, argSystem, argLocation, argVersion, argConfig) returning true $$;


ALTER FUNCTION public.addconfig(argclientid bigint, argnet text, argsystem text, arglocation text, argversion integer, argconfig bytea) OWNER TO msrv;

--
-- Name: addconstcommand(bigint, text, text, text, integer, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addconstcommand(argclientid bigint, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into ConstCommands (ClientID, Net, System, Location, Command, Param) values (argClientID, argNet, argSystem, argLocation, argCommand, argParam) returning true $$;


ALTER FUNCTION public.addconstcommand(argclientid bigint, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) OWNER TO msrv;

--
-- Name: addfile(text, bytea, boolean, bigint, text, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addfile(argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into Files (Name, Data, Public, ClientID, Net, System, Location) values (argName, argData, argPublic, argClientID, argNet, argSystem, argLocation) returning true $$;


ALTER FUNCTION public.addfile(argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) OWNER TO msrv;

--
-- Name: addfilewithbackup(text, bytea, boolean, bigint, text, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addfilewithbackup(argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) RETURNS void
    LANGUAGE plpgsql
    AS $$ declare rowid bigint; begin select ID into rowid from Files where Name=argName and Public=argPublic and ClientID=argClientID and Net=argNet and System=argSystem and Location=argLocation; if rowid is not null then update Files set Name='bak_'||Name||'_'||(now()::timestamp without time zone)::text where ID=rowid; end if; insert into Files (Name, Data, Public, ClientID, Net, System, Location) values (argName, argData, argPublic, argClientID, argNet, argSystem, argLocation); end;$$;


ALTER FUNCTION public.addfilewithbackup(argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) OWNER TO msrv;

--
-- Name: addidlecommand(integer, text, text, text, integer, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addidlecommand(argcount integer, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) RETURNS boolean
    LANGUAGE sql
    AS $$ with Block as (select (extract(epoch from now())*100000)::bigint as ID) insert into IdleCommands (Block, Net, System, Location, Command, Param) select (select ID from Block), argNet, argSystem, argLocation, argCommand, argParam from (select generate_series(1, argCount)) q returning true $$;


ALTER FUNCTION public.addidlecommand(argcount integer, argnet text, argsystem text, arglocation text, argcommand integer, argparam text) OWNER TO msrv;

--
-- Name: addidlecommandsblock(bigint, integer); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addidlecommandsblock(argblock bigint, argcount integer) RETURNS boolean
    LANGUAGE sql
    AS $$ with b as (select distinct on (Block) Block, Net, System, Location, Command, Param from IdleCommands where Block=argBlock) insert into IdleCommands (Block, Net, System, Location, Command, Param) select Block, Net, System, Location, Command, Param from b, (select generate_series(1, argCount)) q returning true $$;


ALTER FUNCTION public.addidlecommandsblock(argblock bigint, argcount integer) OWNER TO msrv;

--
-- Name: addremoteuser(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addremoteuser(argname text, argpassword text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into RemoteUsers (Name, Password) values (argName, argPassword) returning true $$;


ALTER FUNCTION public.addremoteuser(argname text, argpassword text) OWNER TO msrv;

--
-- Name: addremoteuserip(text, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addremoteuserip(argname text, argaddrfrom text, argaddrto text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into RemoteIP (Name, AddrFrom, AddrTo) values (argName, argAddrFrom::inet, argAddrTo::inet) returning true $$;


ALTER FUNCTION public.addremoteuserip(argname text, argaddrfrom text, argaddrto text) OWNER TO msrv;

--
-- Name: addremoteuserproc(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addremoteuserproc(argname text, argproc text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into RemoteProc (Name, Proc) values (argName, argProc) returning true $$;


ALTER FUNCTION public.addremoteuserproc(argname text, argproc text) OWNER TO msrv;

--
-- Name: addsilent(text, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addsilent(argnet text, argsystem text, arglocation text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into Silent (Net, System, Location) values (argNet, argSystem, argLocation) returning true $$;


ALTER FUNCTION public.addsilent(argnet text, argsystem text, arglocation text) OWNER TO msrv;

--
-- Name: addvarscommand(bigint, text, text, text, text, text, integer, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION addvarscommand(argclientid bigint, argnet text, argsystem text, arglocation text, argname text, argvalue text, argcommand integer, argparam text) RETURNS boolean
    LANGUAGE sql
    AS $$ insert into VarsCommands (ClientID, Net, System, Location, Name, Value, Command, Param) values (argClientID, argNet, argSystem, argLocation, argName, argValue, argCommand, argParam) returning true $$;


ALTER FUNCTION public.addvarscommand(argclientid bigint, argnet text, argsystem text, arglocation text, argname text, argvalue text, argcommand integer, argparam text) OWNER TO msrv;

--
-- Name: deleteautosilentip(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteautosilentip(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from AutoSilentIP where ID=argID returning true $$;


ALTER FUNCTION public.deleteautosilentip(argid bigint) OWNER TO msrv;

--
-- Name: deleteautosilentprefix(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteautosilentprefix(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from AutoSilentPrefix where ID=argID returning true $$;


ALTER FUNCTION public.deleteautosilentprefix(argid bigint) OWNER TO msrv;

--
-- Name: deleteautosilentvars(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteautosilentvars(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from AutoSilentVars where ID=argID returning true $$;


ALTER FUNCTION public.deleteautosilentvars(argid bigint) OWNER TO msrv;

--
-- Name: deletebackconndata(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletebackconndata(argclientid bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$ declare cl record; begin for cl in select distinct on(ClientID) ClientID from BackConnData where (case when argClientID=0 then 1=1 else ClientID=argClientID end) loop delete from BackConnData where ID in (select ID from BackConnData where ClientID=cl.ClientID order by DateTime limit (select count(1) from BackConnData where ClientID=cl.ClientID)*3/4); end loop;end;$$;


ALTER FUNCTION public.deletebackconndata(argclientid bigint) OWNER TO msrv;

--
-- Name: deletebcserver(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletebcserver(argname text) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from BackConnServers where Name=argName returning true $$;


ALTER FUNCTION public.deletebcserver(argname text) OWNER TO msrv;

--
-- Name: deleteconfig(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteconfig(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from Configs where ID=argID returning true $$;


ALTER FUNCTION public.deleteconfig(argid bigint) OWNER TO msrv;

--
-- Name: deleteconstcommand(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteconstcommand(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from ConstCommands where ID=argID returning true $$;


ALTER FUNCTION public.deleteconstcommand(argid bigint) OWNER TO msrv;

--
-- Name: deletefile(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletefile(argname text) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from Files where Name=argName returning true $$;


ALTER FUNCTION public.deletefile(argname text) OWNER TO msrv;

--
-- Name: deleteidlecommandsblock(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteidlecommandsblock(argblock bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from IdleCommands where Block=argBlock returning true $$;


ALTER FUNCTION public.deleteidlecommandsblock(argblock bigint) OWNER TO msrv;

--
-- Name: deletelog(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletelog(argclientid bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$ declare cl record; begin for cl in select distinct on(ClientID) ClientID from Log where (case when argClientID=0 then 1=1 else ClientID=argClientID end) loop delete from Log where ID in (select ID from Log where ClientID=cl.ClientID order by DateTime limit (select count(1) from Log where ClientID=cl.ClientID)*3/4); end loop;end;$$;


ALTER FUNCTION public.deletelog(argclientid bigint) OWNER TO msrv;

--
-- Name: deleteremoteuser(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteremoteuser(argname text) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from RemoteUsers where Name=argName returning true $$;


ALTER FUNCTION public.deleteremoteuser(argname text) OWNER TO msrv;

--
-- Name: deleteremoteuserip(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteremoteuserip(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from RemoteIP where ID=argID returning true $$;


ALTER FUNCTION public.deleteremoteuserip(argid bigint) OWNER TO msrv;

--
-- Name: deleteremoteuserproc(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deleteremoteuserproc(argname text, argproc text) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from RemoteProc where Name=argName and Proc=argProc returning true $$;


ALTER FUNCTION public.deleteremoteuserproc(argname text, argproc text) OWNER TO msrv;

--
-- Name: deletesilent(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletesilent(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from Silent where ID=argID returning true $$;


ALTER FUNCTION public.deletesilent(argid bigint) OWNER TO msrv;

--
-- Name: deletevars(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletevars(argclientid bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$ declare cl record; begin for cl in select distinct on(ClientID) ClientID from Vars where (case when argClientID=0 then 1=1 else ClientID=argClientID end) loop delete from Vars where ID in (select ID from Vars where ClientID=cl.ClientID order by DateTime limit (select count(1) from Vars where ClientID=cl.ClientID)*3/4); end loop;end;$$;


ALTER FUNCTION public.deletevars(argclientid bigint) OWNER TO msrv;

--
-- Name: deletevarscommand(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION deletevarscommand(argid bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ delete from VarsCommands where ID=argID returning true $$;


ALTER FUNCTION public.deletevarscommand(argid bigint) OWNER TO msrv;

--
-- Name: disableidlecommandsblock(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION disableidlecommandsblock(argblock bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ update IdleCommands set Enabled=false where Block=argBlock returning true $$;


ALTER FUNCTION public.disableidlecommandsblock(argblock bigint) OWNER TO msrv;

--
-- Name: enableidlecommandsblock(bigint); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION enableidlecommandsblock(argblock bigint) RETURNS boolean
    LANGUAGE sql
    AS $$ update IdleCommands set Enabled=true where Block=argBlock returning true $$;


ALTER FUNCTION public.enableidlecommandsblock(argblock bigint) OWNER TO msrv;

--
-- Name: getautosilentip(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getautosilentip() RETURNS TABLE(id bigint, addrfrom text, addrto text)
    LANGUAGE sql
    AS $$ select ID, AddrFrom::text, AddrTo::text from AutoSilentIP order by ID $$;


ALTER FUNCTION public.getautosilentip() OWNER TO msrv;

--
-- Name: getautosilentprefix(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getautosilentprefix() RETURNS TABLE(id bigint, regex text)
    LANGUAGE sql
    AS $$ select ID, RegEx from AutoSilentPrefix order by ID $$;


ALTER FUNCTION public.getautosilentprefix() OWNER TO msrv;

--
-- Name: getautosilentvars(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getautosilentvars() RETURNS TABLE(id bigint, name text, value text)
    LANGUAGE sql
    AS $$ select ID, Name, Value from AutoSilentVars order by ID $$;


ALTER FUNCTION public.getautosilentvars() OWNER TO msrv;

--
-- Name: getbcservers(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getbcservers() RETURNS TABLE(name text, ip text, port integer, password1 text, password2 text)
    LANGUAGE sql
    AS $$ select Name, IP::text, Port, Password1, Password2 from BackConnServers $$;


ALTER FUNCTION public.getbcservers() OWNER TO msrv;

--
-- Name: getclientvars(bigint, boolean); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getclientvars(argclientid bigint, argall boolean) RETURNS TABLE(name text, value text, ttl integer, datetime text)
    LANGUAGE sql
    AS $$ select Name, Value, TTL, DateTime::text from Vars where ClientID=argClientID and DateTime>=case when argAll=true then '0001-01-01' else (select LastRegistration from Clients where ClientID=argClientID) end order by DateTime $$;


ALTER FUNCTION public.getclientvars(argclientid bigint, argall boolean) OWNER TO msrv;

--
-- Name: getconfigs(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getconfigs() RETURNS TABLE(id bigint, datetime text, clientid bigint, net text, system text, location text, version integer)
    LANGUAGE sql
    AS $$ select ID, DateTime::text, ClientID, Net, System, Location::text, Version from Configs order by ID $$;


ALTER FUNCTION public.getconfigs() OWNER TO msrv;

--
-- Name: getconstcommands(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getconstcommands() RETURNS TABLE(id bigint, clientid bigint, net text, system text, location text, command integer, param text)
    LANGUAGE sql
    AS $$ select ID, ClientID, Net, System, Location::text, Command, Param from ConstCommands order by ID $$;


ALTER FUNCTION public.getconstcommands() OWNER TO msrv;

--
-- Name: getcountries(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getcountries() RETURNS TABLE(country text, code character)
    LANGUAGE sql
    AS $$ select distinct Country, Code from Location order by Country $$;


ALTER FUNCTION public.getcountries() OWNER TO msrv;

--
-- Name: getfiles(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getfiles() RETURNS TABLE(id bigint, name text, public boolean, clientid bigint, net text, system text, location text)
    LANGUAGE sql
    AS $$ select ID, Name, Public, ClientID, Net, System, Location::text from Files order by ID $$;


ALTER FUNCTION public.getfiles() OWNER TO msrv;

--
-- Name: getidlecommandsinfo(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getidlecommandsinfo() RETURNS TABLE(block bigint, completed bigint, total bigint, net text, system text, location character, command integer, param text)
    LANGUAGE sql
    AS $$ select a.Block, a.Completed, a.Total, b.Net, b.System, b.Location, b.Command, b.Param from (select Block, count(case when ClientID is not null then 1 end) as Completed, count(1) as Total from IdleCommands group by Block) a, (select distinct on(Block) Block, Net, System, Location, Command, Param from IdleCommands) b where a.Block=b.Block order by a.Block $$;


ALTER FUNCTION public.getidlecommandsinfo() OWNER TO msrv;

--
-- Name: getlog(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getlog() RETURNS TABLE(datetime text, clientid bigint, command integer, commandid bigint, result integer, comment text)
    LANGUAGE sql
    AS $$ select DateTime::text, ClientID, Command, CommandID, Result, Comment from Log order by ID $$;


ALTER FUNCTION public.getlog() OWNER TO msrv;

--
-- Name: getremoteuserip(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getremoteuserip(argname text) RETURNS TABLE(id bigint, addrfrom text, addrto text)
    LANGUAGE sql
    AS $$ select ID, AddrFrom::text, AddrTo::text from RemoteIP where Name=argName order by AddrFrom $$;


ALTER FUNCTION public.getremoteuserip(argname text) OWNER TO msrv;

--
-- Name: getremoteuserproc(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getremoteuserproc(argname text) RETURNS TABLE(proc text)
    LANGUAGE sql
    AS $$ select Proc from RemoteProc where Name=argName order by Proc $$;


ALTER FUNCTION public.getremoteuserproc(argname text) OWNER TO msrv;

--
-- Name: getremoteusers(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getremoteusers() RETURNS TABLE(name text, password text)
    LANGUAGE sql
    AS $$ select Name, Password from RemoteUsers $$;


ALTER FUNCTION public.getremoteusers() OWNER TO msrv;

--
-- Name: getsilent(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getsilent() RETURNS TABLE(id bigint, net text, system text, location character)
    LANGUAGE sql
    AS $$ select ID, Net, System, Location from Silent order by ID $$;


ALTER FUNCTION public.getsilent() OWNER TO msrv;

--
-- Name: getvarscommands(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION getvarscommands() RETURNS TABLE(id bigint, clientid bigint, net text, system text, location text, name text, value text, command integer, param text)
    LANGUAGE sql
    AS $$ select ID, ClientID, Net, System, Location::text, Name, Value, Command, Param from VarsCommands order by ID $$;


ALTER FUNCTION public.getvarscommands() OWNER TO msrv;

--
-- Name: gmeval(text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION gmeval(expression text) RETURNS integer
    LANGUAGE plpgsql
    AS $$ declare   result integer; begin   execute expression into result;   return result; end; $$;


ALTER FUNCTION public.gmeval(expression text) OWNER TO msrv;

--
-- Name: processvarsinsert(); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION processvarsinsert() RETURNS trigger
    LANGUAGE plpgsql
    AS $$ declare cl record; begin select Net, System, Location into cl from Clients where ClientID=new.ClientID; insert into Commands (ClientID, Command, Param) select new.ClientID, Command, Param from VarsCommands where (ClientID=new.ClientID or ClientID=0) and (Net=cl.Net or Net='*') and (System=cl.System or System='*') and (Location=cl.Location or Location='*') and (Name=new.Name) and (Value=new.Value) order by ID; return null; end; $$;


ALTER FUNCTION public.processvarsinsert() OWNER TO msrv;

--
-- Name: setautosilentprefixchanged(boolean); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION setautosilentprefixchanged(argenable boolean) RETURNS boolean
    LANGUAGE sql
    AS $$ update Settings set Data=(case when argEnable then '1' else '0' end) where Name='AutoSilent Prefix Changed' returning true $$;


ALTER FUNCTION public.setautosilentprefixchanged(argenable boolean) OWNER TO msrv;

--
-- Name: setclientsilent(bigint, text, text, text, boolean); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION setclientsilent(argclientid bigint, argnet text, argsystem text, arglocation text, argsilent boolean) RETURNS boolean
    LANGUAGE sql
    AS $$ update Clients set Silent=argSilent where (argClientID=0 or argClientID=ClientID) and (argNet='*' or argNet=Net) and (argSystem='*' or argSystem=System) and (argLocation='*' or argLocation=Location) returning true $$;


ALTER FUNCTION public.setclientsilent(argclientid bigint, argnet text, argsystem text, arglocation text, argsilent boolean) OWNER TO msrv;

--
-- Name: subidlecommandsblock(bigint, integer); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION subidlecommandsblock(argblock bigint, argcount integer) RETURNS boolean
    LANGUAGE sql
    AS $$ with b as (select ID from IdleCommands where ClientID is null and Block=argBlock limit argCount) delete from IdleCommands where Block=argBlock and ID in (select ID from b) returning true $$;


ALTER FUNCTION public.subidlecommandsblock(argblock bigint, argcount integer) OWNER TO msrv;

--
-- Name: updatebcserver(text, text, integer, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION updatebcserver(argname text, argip text, argport integer, argpassword1 text, argpassword2 text) RETURNS boolean
    LANGUAGE sql
    AS $$ update BackConnServers set IP=case argIP when '' then IP else argIP::inet end, Port=case argPort when -1 then Port else argPort end, Password1=case argPassword1 when '' then Password1 else argPassword1 end, Password2=case argPassword2 when '' then Password2 else argPassword2 end where Name=argName returning true $$;


ALTER FUNCTION public.updatebcserver(argname text, argip text, argport integer, argpassword1 text, argpassword2 text) OWNER TO msrv;

--
-- Name: updatefile(bigint, text, bytea, boolean, bigint, text, text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION updatefile(argid bigint, argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) RETURNS boolean
    LANGUAGE sql
    AS $$ update Files set Name=argName, Data=argData, Public=argPublic, ClientID=argClientID, Net=argNet, System=argSystem, Location=argLocation where ID=argID returning true $$;


ALTER FUNCTION public.updatefile(argid bigint, argname text, argdata bytea, argpublic boolean, argclientid bigint, argnet text, argsystem text, arglocation text) OWNER TO msrv;

--
-- Name: updateremoteuser(text, text); Type: FUNCTION; Schema: public; Owner: msrv
--

CREATE FUNCTION updateremoteuser(argname text, argpassword text) RETURNS boolean
    LANGUAGE sql
    AS $$ update RemoteUsers set Password=argPassword where Name=argName returning true $$;


ALTER FUNCTION public.updateremoteuser(argname text, argpassword text) OWNER TO msrv;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: actions; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE actions (
    id integer NOT NULL,
    name character varying,
    description character varying
);


ALTER TABLE public.actions OWNER TO msrv;

--
-- Name: actions_roles; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE actions_roles (
    id integer NOT NULL,
    role_id integer,
    action_id integer
);


ALTER TABLE public.actions_roles OWNER TO msrv;

--
-- Name: actions_roles_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE actions_roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.actions_roles_id_seq OWNER TO msrv;

--
-- Name: actions_roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE actions_roles_id_seq OWNED BY actions_roles.id;


--
-- Name: autosilentip; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE autosilentip (
    id bigint NOT NULL,
    addrfrom inet NOT NULL,
    addrto inet NOT NULL
);


ALTER TABLE public.autosilentip OWNER TO msrv;

--
-- Name: autosilentip_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE autosilentip_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.autosilentip_id_seq OWNER TO msrv;

--
-- Name: autosilentip_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE autosilentip_id_seq OWNED BY autosilentip.id;


--
-- Name: autosilentprefix; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE autosilentprefix (
    id bigint NOT NULL,
    regex text NOT NULL
);


ALTER TABLE public.autosilentprefix OWNER TO msrv;

--
-- Name: autosilentprefix_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE autosilentprefix_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.autosilentprefix_id_seq OWNER TO msrv;

--
-- Name: autosilentprefix_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE autosilentprefix_id_seq OWNED BY autosilentprefix.id;


--
-- Name: autosilentvars; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE autosilentvars (
    id bigint NOT NULL,
    name text NOT NULL,
    value text NOT NULL
);


ALTER TABLE public.autosilentvars OWNER TO msrv;

--
-- Name: autosilentvars_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE autosilentvars_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.autosilentvars_id_seq OWNER TO msrv;

--
-- Name: autosilentvars_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE autosilentvars_id_seq OWNED BY autosilentvars.id;


--
-- Name: backconndata; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE backconndata (
    id bigint NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    name text NOT NULL,
    clientid bigint NOT NULL,
    ip inet,
    port integer,
    operation character varying(8)
);


ALTER TABLE public.backconndata OWNER TO msrv;

--
-- Name: backconndata_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE backconndata_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.backconndata_id_seq OWNER TO msrv;

--
-- Name: backconndata_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE backconndata_id_seq OWNED BY backconndata.id;


--
-- Name: backconnservers; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE backconnservers (
    name text NOT NULL,
    ip inet,
    port integer,
    password1 text,
    password2 text
);


ALTER TABLE public.backconnservers OWNER TO msrv;

--
-- Name: clients; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE clients (
    clientid bigint NOT NULL,
    cid3 bigint DEFAULT 0 NOT NULL,
    cid2 bigint DEFAULT 0 NOT NULL,
    cid1 bigint DEFAULT 0 NOT NULL,
    cid0 bigint DEFAULT 0 NOT NULL,
    prefix text,
    net text,
    system text,
    ip inet,
    location character(2),
    version integer,
    registered timestamp without time zone,
    lastregistration timestamp without time zone,
    lastactivity timestamp without time zone,
    silent boolean DEFAULT false
);


ALTER TABLE public.clients OWNER TO msrv;

--
-- Name: clients_clientid_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE clients_clientid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.clients_clientid_seq OWNER TO msrv;

--
-- Name: clients_clientid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE clients_clientid_seq OWNED BY clients.clientid;


--
-- Name: commands; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE commands (
    id bigint NOT NULL,
    clientid bigint NOT NULL,
    command integer,
    param text
);


ALTER TABLE public.commands OWNER TO msrv;

--
-- Name: commands_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE commands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.commands_id_seq OWNER TO msrv;

--
-- Name: commands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE commands_id_seq OWNED BY commands.id;


--
-- Name: configs; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE configs (
    id bigint NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    clientid bigint DEFAULT 0 NOT NULL,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL,
    version integer,
    config bytea
);


ALTER TABLE public.configs OWNER TO msrv;

--
-- Name: configs_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE configs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.configs_id_seq OWNER TO msrv;

--
-- Name: configs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE configs_id_seq OWNED BY configs.id;


--
-- Name: constcommands; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE constcommands (
    id bigint NOT NULL,
    clientid bigint DEFAULT 0 NOT NULL,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL,
    command integer,
    param text
);


ALTER TABLE public.constcommands OWNER TO msrv;

--
-- Name: constcommands_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE constcommands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.constcommands_id_seq OWNER TO msrv;

--
-- Name: constcommands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE constcommands_id_seq OWNED BY constcommands.id;


--
-- Name: databrowser; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE databrowser (
    id bigint NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    clientid bigint NOT NULL,
    data bytea
);


ALTER TABLE public.databrowser OWNER TO msrv;

--
-- Name: databrowser_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE databrowser_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.databrowser_id_seq OWNER TO msrv;

--
-- Name: databrowser_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE databrowser_id_seq OWNED BY databrowser.id;


--
-- Name: datafiles; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE datafiles (
    datetime timestamp without time zone DEFAULT now(),
    name text,
    data bytea,
    sha1 character varying(40) NOT NULL,
    clientid bigint DEFAULT 0 NOT NULL
);


ALTER TABLE public.datafiles OWNER TO msrv;

--
-- Name: datageneral; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE datageneral (
    id bigint NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    clientid bigint NOT NULL,
    data text
);


ALTER TABLE public.datageneral OWNER TO msrv;

--
-- Name: datageneral_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE datageneral_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.datageneral_id_seq OWNER TO msrv;

--
-- Name: datageneral_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE datageneral_id_seq OWNED BY datageneral.id;


--
-- Name: debugstat; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE debugstat (
    host text NOT NULL,
    requests bigint,
    inbytes bigint,
    outbutes bigint
);


ALTER TABLE public.debugstat OWNER TO msrv;

--
-- Name: files; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE files (
    id bigint NOT NULL,
    name text NOT NULL,
    data bytea,
    public boolean DEFAULT false NOT NULL,
    clientid bigint DEFAULT 0 NOT NULL,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL
);


ALTER TABLE public.files OWNER TO msrv;

--
-- Name: files_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE files_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.files_id_seq OWNER TO msrv;

--
-- Name: files_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE files_id_seq OWNED BY files.id;


--
-- Name: idlecommands; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE idlecommands (
    id bigint NOT NULL,
    block bigint NOT NULL,
    clientid bigint,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL,
    command integer NOT NULL,
    param text DEFAULT ''::text NOT NULL,
    enabled boolean DEFAULT true
);


ALTER TABLE public.idlecommands OWNER TO msrv;

--
-- Name: idlecommands_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE idlecommands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.idlecommands_id_seq OWNER TO msrv;

--
-- Name: idlecommands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE idlecommands_id_seq OWNED BY idlecommands.id;


--
-- Name: location; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE location (
    addrfrom inet NOT NULL,
    addrto inet NOT NULL,
    country text,
    code character(2)
);


ALTER TABLE public.location OWNER TO msrv;

--
-- Name: log; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE log (
    id bigint NOT NULL,
    datetime timestamp without time zone DEFAULT now(),
    clientid bigint NOT NULL,
    commandid bigint,
    command integer,
    result integer,
    comment text
);


ALTER TABLE public.log OWNER TO msrv;

--
-- Name: log_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.log_id_seq OWNER TO msrv;

--
-- Name: log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE log_id_seq OWNED BY log.id;


--
-- Name: remoteip; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE remoteip (
    id bigint NOT NULL,
    name text NOT NULL,
    addrfrom inet NOT NULL,
    addrto inet NOT NULL
);


ALTER TABLE public.remoteip OWNER TO msrv;

--
-- Name: remoteip_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE remoteip_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.remoteip_id_seq OWNER TO msrv;

--
-- Name: remoteip_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE remoteip_id_seq OWNED BY remoteip.id;


--
-- Name: remoteproc; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE remoteproc (
    name text NOT NULL,
    proc text
);


ALTER TABLE public.remoteproc OWNER TO msrv;

--
-- Name: remoteusers; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE remoteusers (
    name text NOT NULL,
    password text
);


ALTER TABLE public.remoteusers OWNER TO msrv;

--
-- Name: roles; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE roles (
    id integer NOT NULL,
    name character varying(32) NOT NULL,
    description character varying
);


ALTER TABLE public.roles OWNER TO msrv;

--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.roles_id_seq OWNER TO msrv;

--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE roles_id_seq OWNED BY roles.id;


--
-- Name: roles_users; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE roles_users (
    user_id integer,
    role_id integer
);


ALTER TABLE public.roles_users OWNER TO msrv;

--
-- Name: settings; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE settings (
    name text NOT NULL,
    data text
);


ALTER TABLE public.settings OWNER TO msrv;

--
-- Name: silent; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE silent (
    id bigint NOT NULL,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL
);


ALTER TABLE public.silent OWNER TO msrv;

--
-- Name: silent_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE silent_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.silent_id_seq OWNER TO msrv;

--
-- Name: silent_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE silent_id_seq OWNED BY silent.id;


--
-- Name: user_tokens; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE user_tokens (
    id integer NOT NULL,
    user_id integer NOT NULL,
    user_agent character varying(40) NOT NULL,
    token character varying NOT NULL,
    created integer NOT NULL,
    expires integer NOT NULL
);


ALTER TABLE public.user_tokens OWNER TO msrv;

--
-- Name: user_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE user_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.user_tokens_id_seq OWNER TO msrv;

--
-- Name: user_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE user_tokens_id_seq OWNED BY user_tokens.id;


--
-- Name: users; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE users (
    id integer NOT NULL,
    username character varying(32) NOT NULL,
    password character varying(64) NOT NULL,
    logins integer DEFAULT 0 NOT NULL,
    last_login integer
);


ALTER TABLE public.users OWNER TO msrv;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.users_id_seq OWNER TO msrv;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE users_id_seq OWNED BY users.id;


--
-- Name: userslogs; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE userslogs (
    id integer NOT NULL,
    data character varying,
    "timestamp" timestamp without time zone DEFAULT now(),
    "user" character varying
);


ALTER TABLE public.userslogs OWNER TO msrv;

--
-- Name: userslogs_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE userslogs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.userslogs_id_seq OWNER TO msrv;

--
-- Name: userslogs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE userslogs_id_seq OWNED BY userslogs.id;


--
-- Name: vars; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE vars (
    id bigint NOT NULL,
    clientid bigint NOT NULL,
    name text NOT NULL,
    value text NOT NULL,
    ttl integer,
    datetime timestamp without time zone DEFAULT now()
);


ALTER TABLE public.vars OWNER TO msrv;

--
-- Name: vars_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE vars_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.vars_id_seq OWNER TO msrv;

--
-- Name: vars_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE vars_id_seq OWNED BY vars.id;


--
-- Name: varscommands; Type: TABLE; Schema: public; Owner: msrv; Tablespace: 
--

CREATE TABLE varscommands (
    id bigint NOT NULL,
    clientid bigint DEFAULT 0 NOT NULL,
    net text DEFAULT '*'::text NOT NULL,
    system text DEFAULT '*'::text NOT NULL,
    location character(2) DEFAULT '*'::bpchar NOT NULL,
    name text,
    command integer,
    param text,
    value text
);


ALTER TABLE public.varscommands OWNER TO msrv;

--
-- Name: varscommands_id_seq; Type: SEQUENCE; Schema: public; Owner: msrv
--

CREATE SEQUENCE varscommands_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.varscommands_id_seq OWNER TO msrv;

--
-- Name: varscommands_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: msrv
--

ALTER SEQUENCE varscommands_id_seq OWNED BY varscommands.id;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY actions_roles ALTER COLUMN id SET DEFAULT nextval('actions_roles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY autosilentip ALTER COLUMN id SET DEFAULT nextval('autosilentip_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY autosilentprefix ALTER COLUMN id SET DEFAULT nextval('autosilentprefix_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY autosilentvars ALTER COLUMN id SET DEFAULT nextval('autosilentvars_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY backconndata ALTER COLUMN id SET DEFAULT nextval('backconndata_id_seq'::regclass);


--
-- Name: clientid; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY clients ALTER COLUMN clientid SET DEFAULT nextval('clients_clientid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY commands ALTER COLUMN id SET DEFAULT nextval('commands_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY configs ALTER COLUMN id SET DEFAULT nextval('configs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY constcommands ALTER COLUMN id SET DEFAULT nextval('constcommands_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY databrowser ALTER COLUMN id SET DEFAULT nextval('databrowser_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY datageneral ALTER COLUMN id SET DEFAULT nextval('datageneral_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY files ALTER COLUMN id SET DEFAULT nextval('files_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY idlecommands ALTER COLUMN id SET DEFAULT nextval('idlecommands_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY log ALTER COLUMN id SET DEFAULT nextval('log_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY remoteip ALTER COLUMN id SET DEFAULT nextval('remoteip_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY roles ALTER COLUMN id SET DEFAULT nextval('roles_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY silent ALTER COLUMN id SET DEFAULT nextval('silent_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY user_tokens ALTER COLUMN id SET DEFAULT nextval('user_tokens_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY users ALTER COLUMN id SET DEFAULT nextval('users_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY userslogs ALTER COLUMN id SET DEFAULT nextval('userslogs_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY vars ALTER COLUMN id SET DEFAULT nextval('vars_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY varscommands ALTER COLUMN id SET DEFAULT nextval('varscommands_id_seq'::regclass);


--
-- Name: actions_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY actions
    ADD CONSTRAINT actions_pkey PRIMARY KEY (id);


--
-- Name: autosilentip_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY autosilentip
    ADD CONSTRAINT autosilentip_pkey PRIMARY KEY (id);


--
-- Name: autosilentprefix_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY autosilentprefix
    ADD CONSTRAINT autosilentprefix_pkey PRIMARY KEY (id);


--
-- Name: autosilentvars_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY autosilentvars
    ADD CONSTRAINT autosilentvars_pkey PRIMARY KEY (id);


--
-- Name: backconndata_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY backconndata
    ADD CONSTRAINT backconndata_pkey PRIMARY KEY (id);


--
-- Name: backconnservers_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY backconnservers
    ADD CONSTRAINT backconnservers_pkey PRIMARY KEY (name);


--
-- Name: clients_cid3_cid2_cid1_cid0_key; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_cid3_cid2_cid1_cid0_key UNIQUE (cid3, cid2, cid1, cid0);


--
-- Name: clients_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY clients
    ADD CONSTRAINT clients_pkey PRIMARY KEY (clientid);


--
-- Name: commands_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY commands
    ADD CONSTRAINT commands_pkey PRIMARY KEY (id);


--
-- Name: configs_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY configs
    ADD CONSTRAINT configs_pkey PRIMARY KEY (id);


--
-- Name: constcommands_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY constcommands
    ADD CONSTRAINT constcommands_pkey PRIMARY KEY (id);


--
-- Name: databrowser_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY databrowser
    ADD CONSTRAINT databrowser_pkey PRIMARY KEY (id);


--
-- Name: datafiles_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY datafiles
    ADD CONSTRAINT datafiles_pkey PRIMARY KEY (sha1);


--
-- Name: datageneral_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY datageneral
    ADD CONSTRAINT datageneral_pkey PRIMARY KEY (id);


--
-- Name: debugstat_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY debugstat
    ADD CONSTRAINT debugstat_pkey PRIMARY KEY (host);


--
-- Name: files_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY files
    ADD CONSTRAINT files_pkey PRIMARY KEY (id);


--
-- Name: idlecommands_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY idlecommands
    ADD CONSTRAINT idlecommands_pkey PRIMARY KEY (id);


--
-- Name: log_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY log
    ADD CONSTRAINT log_pkey PRIMARY KEY (id);


--
-- Name: remoteip_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY remoteip
    ADD CONSTRAINT remoteip_pkey PRIMARY KEY (id);


--
-- Name: remoteproc_name_proc_key; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY remoteproc
    ADD CONSTRAINT remoteproc_name_proc_key UNIQUE (name, proc);


--
-- Name: remoteusers_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY remoteusers
    ADD CONSTRAINT remoteusers_pkey PRIMARY KEY (name);


--
-- Name: roles_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: settings_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY settings
    ADD CONSTRAINT settings_pkey PRIMARY KEY (name);


--
-- Name: silent_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY silent
    ADD CONSTRAINT silent_pkey PRIMARY KEY (id);


--
-- Name: user_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY user_tokens
    ADD CONSTRAINT user_tokens_pkey PRIMARY KEY (id);


--
-- Name: users_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: vars_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY vars
    ADD CONSTRAINT vars_pkey PRIMARY KEY (id);


--
-- Name: varscommands_pkey; Type: CONSTRAINT; Schema: public; Owner: msrv; Tablespace: 
--

ALTER TABLE ONLY varscommands
    ADD CONSTRAINT varscommands_pkey PRIMARY KEY (id);


--
-- Name: autosilentip_addrfrom_addrto_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX autosilentip_addrfrom_addrto_idx ON autosilentip USING btree (addrfrom, addrto);


--
-- Name: autosilentprefix_regex_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX autosilentprefix_regex_idx ON autosilentprefix USING btree (regex);


--
-- Name: autosilentvars_name_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX autosilentvars_name_idx ON autosilentvars USING btree (name);


--
-- Name: backconndata_clientid_operation_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX backconndata_clientid_operation_idx ON backconndata USING btree (clientid, operation);


--
-- Name: backconndata_datetime_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX backconndata_datetime_idx ON backconndata USING btree (datetime);


--
-- Name: cid0_cid1; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX cid0_cid1 ON clients USING btree (cid0, cid1);


--
-- Name: clients_clientid_net_system_location_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX clients_clientid_net_system_location_idx ON clients USING btree (clientid, net, system, location);


--
-- Name: commands_clientid_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX commands_clientid_idx ON commands USING btree (clientid);


--
-- Name: configs_clientid_net_system_location_version_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX configs_clientid_net_system_location_version_idx ON configs USING btree (clientid, net, system, location, version);


--
-- Name: configs_datetime_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX configs_datetime_idx ON configs USING btree (datetime);


--
-- Name: databrowser_clientid_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX databrowser_clientid_idx ON databrowser USING btree (clientid);


--
-- Name: datageneral_clientid_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX datageneral_clientid_idx ON datageneral USING btree (clientid);


--
-- Name: files_name_clientid_net_system_location_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX files_name_clientid_net_system_location_idx ON files USING btree (name, clientid, net, system, location);


--
-- Name: idlecommands_clientid_block_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX idlecommands_clientid_block_idx ON idlecommands USING btree (clientid, block);


--
-- Name: location_addrfrom_addrto_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX location_addrfrom_addrto_idx ON location USING btree (addrfrom, addrto);


--
-- Name: log_clientid_command_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX log_clientid_command_idx ON log USING btree (clientid, command);


--
-- Name: log_clientid_id; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX log_clientid_id ON log USING btree (clientid, id);


--
-- Name: role_id_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX role_id_idx ON roles_users USING btree (role_id);


--
-- Name: roles_name_key; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE UNIQUE INDEX roles_name_key ON roles USING btree (name);


--
-- Name: silent_net_system_location_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX silent_net_system_location_idx ON silent USING btree (net, system, location);


--
-- Name: user_id_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX user_id_idx ON roles_users USING btree (user_id);


--
-- Name: user_tokens_token_key; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE UNIQUE INDEX user_tokens_token_key ON user_tokens USING btree (token);


--
-- Name: users_username_key; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE UNIQUE INDEX users_username_key ON users USING btree (username);


--
-- Name: vars_clientid_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX vars_clientid_idx ON vars USING btree (clientid);


--
-- Name: varscommands_clientid_idx; Type: INDEX; Schema: public; Owner: msrv; Tablespace: 
--

CREATE INDEX varscommands_clientid_idx ON varscommands USING btree (clientid);


--
-- Name: triggervarsinsert; Type: TRIGGER; Schema: public; Owner: msrv
--

CREATE TRIGGER triggervarsinsert AFTER INSERT ON vars FOR EACH ROW EXECUTE PROCEDURE processvarsinsert();


--
-- Name: actions_roles_action_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY actions_roles
    ADD CONSTRAINT actions_roles_action_id_fkey FOREIGN KEY (action_id) REFERENCES actions(id) ON DELETE CASCADE;


--
-- Name: actions_roles_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY actions_roles
    ADD CONSTRAINT actions_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE;


--
-- Name: remoteip_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY remoteip
    ADD CONSTRAINT remoteip_name_fkey FOREIGN KEY (name) REFERENCES remoteusers(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: remoteproc_name_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY remoteproc
    ADD CONSTRAINT remoteproc_name_fkey FOREIGN KEY (name) REFERENCES remoteusers(name) ON UPDATE CASCADE ON DELETE CASCADE;


--
-- Name: roles_users_role_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY roles_users
    ADD CONSTRAINT roles_users_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE;


--
-- Name: roles_users_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY roles_users
    ADD CONSTRAINT roles_users_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: user_tokens_user_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: msrv
--

ALTER TABLE ONLY user_tokens
    ADD CONSTRAINT user_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: msrv
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
REVOKE ALL ON SCHEMA public FROM msrv;
GRANT ALL ON SCHEMA public TO msrv;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;

REVOKE DELETE ON public.clients FROM msrv;

--
-- PostgreSQL database dump complete
--


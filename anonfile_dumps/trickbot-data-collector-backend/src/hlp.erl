-module(hlp).

-export([
	clean/0,
	proc_max_queue/0, proc_max_queue/1,
	proc_max_mem/0, proc_max_mem/1,
	proc_max_heap/0, proc_max_heap/1,
	proc_max_reductions/0, proc_max_reductions/1,
	memory/1,
	most_leaky/1, most_leaky/0,
	i/1, i/3,
	delta_memory/0, delta_memory/1
]).

%% useful helpers

clean() ->
	erase(),
	erlang:garbage_collect(),
	lists:foreach(fun(P) -> erlang:garbage_collect(P) end, processes()).

proc_max_queue() ->
	proc_max_queue(10).
proc_max_queue(N) when is_integer(N) ->
	lists:sublist(lists:reverse(lists:keysort(2, [ { Pid, erlang:process_info(Pid, [message_queue_len, memory])} || Pid <- processes(), Pid =/= self() ])), N).

proc_max_heap() ->
	proc_max_heap(10).
proc_max_heap(N) when is_integer(N) ->
	lists:sublist(lists:reverse(lists:keysort(2, [ { Pid, erlang:process_info(Pid, [heap_size, memory])} || Pid <- processes(), Pid =/= self() ])), N).


proc_max_mem() ->
	proc_max_mem(10).
proc_max_mem(N) when is_integer(N) ->
	lists:sublist(lists:reverse(lists:keysort(2, [ { Pid, erlang:process_info(Pid, [memory, message_queue_len])} || Pid <- processes(), Pid =/= self() ])), N).

proc_max_reductions() ->
	proc_max_reductions(10).

proc_max_reductions(N) ->
	ReductionsOld = lists:sort([{P, R} || {P, { reductions, R }} <- [ {Pid, process_info(Pid, reductions)} || Pid <- processes(), Pid =/= self() ]]),
	timer:sleep(1000),
	ReductionsNew = lists:sort([{P, R} || {P, { reductions, R }} <- [ {Pid, process_info(Pid, reductions)} || Pid <- processes(), Pid =/= self() ]]),
	Delta = max_reduction_loop(ReductionsOld, ReductionsNew, []),
	lists:sublist(lists:reverse(lists:keysort(2, Delta)),N).

max_reduction_loop([], _, Acc) -> Acc;
max_reduction_loop(_, [], Acc) -> Acc;
max_reduction_loop([{P, R1}|List1], [{P,R2}|List2], Acc) ->
	max_reduction_loop(List1, List2, [{P, R2-R1}|Acc]);
max_reduction_loop([H1|_List1] = L1, [H2|List2], Acc) when H1 < H2 ->
	max_reduction_loop(L1, List2, Acc);
max_reduction_loop([H1|List1], [H2|_List2] = L2, Acc) when H1 > H2 ->
	max_reduction_loop(List1, L2, Acc).

memory(Pids) ->
    MemTotal = lists:foldl(fun(Pid, MemCount) ->
        CurrNode = node(),
        Info = case node(Pid) of 
            CurrNode -> erlang:process_info(Pid, [memory]);
            Node -> rpc:call(Node, erlang, process_info, [Pid, [memory]])
        end, 
        Mem = case Info of 
            [{_, Memory}] -> Memory;
            _ -> 0
        end, 
        MemCount + Mem
    end, 0, Pids),
    { MemTotal, length(Pids) }.

most_leaky(N) ->
    lists:sublist(
     lists:usort(
         fun({K1,V1},{K2,V2}) -> {V1,K1} =< {V2,K2} end,
         [try
              {_,Pre} = erlang:process_info(Pid, binary),
              erlang:garbage_collect(Pid),
              {_,Post} = erlang:process_info(Pid, binary),
              {Pid, length(Post)-length(Pre)}
          catch
              _:_ -> {Pid, 0}
          end || Pid <- processes()]),
     N).

most_leaky() ->
	most_leaky(10).

i(A,B,C) ->
	Pid = erlang:list_to_pid(lists:flatten(io_lib:format("<~b.~b.~b>", [ A,B,C ]))),
	i(Pid).

i(Pid) ->
	erlang:process_info(Pid).

delta_memory() ->
	delta_memory(5).

delta_memory(Timeout) when is_integer(Timeout) ->
  M = erlang:memory(),
  timer:sleep(Timeout*1000),
  delta_memory(M);

delta_memory(M) when is_list(M) ->
  lists:map(fun({Key, Value}) ->
		{ Key, Value - proplists:get_value(Key, M, 0) }
	end, erlang:memory()).

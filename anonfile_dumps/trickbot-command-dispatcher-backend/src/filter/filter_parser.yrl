Header
"%%"
"%% @Author: Sergey Loguntsov <loguntsov@gmail.com>"
"%%"
"%%".

Nonterminals
    expression exp_el
    logical logical_exp
    str_exp str_operation
    list list_items
    value
.

Terminals str identifier '&' '|' '!' '>' '<' '>=' '=<' '=' '!=' '[' ']' ',' '(' ')' '++' '~' '!~'
.
Rootsymbol expression
.
expression -> logical : '$1'.
expression -> logical_exp : '$1'.
expression -> value : '$1'.

exp_el -> logical : '$1'.
exp_el -> '(' exp_el ')' : '$2'.
exp_el -> str_exp : '$1'.

logical -> logical_exp '&' logical_exp : {'&', '$1', '$3' }.
logical -> logical_exp '|' logical_exp : {'|', '$1', '$3' }.

logical_exp -> exp_el '=' '|' list  : {{mult, '=', '|'}, '$1', '$4' }.
logical_exp -> exp_el '=' '&' list  : {{mult, '=', '&'}, '$1', '$4' }.

logical_exp -> exp_el '!=' '|' list  : {{mult, '!=', '|'}, '$1', '$4'}.
logical_exp -> exp_el '!=' '&' list  : {{mult, '!=', '&'}, '$1', '$4'}.

logical_exp -> exp_el '~' '|' list  : {{mult, '~', '|'}, '$1', '$4' }.
logical_exp -> exp_el '~' '&' list  : {{mult, '~', '&'}, '$1', '$4' }.

logical_exp -> exp_el '!~' '|' list  : {{mult, '!~', '|'}, '$1', '$4' }.
logical_exp -> exp_el '!~' '&' list  : {{mult, '!~', '&'}, '$1', '$4' }.

logical_exp -> exp_el '=' exp_el : { '=', '$1', '$3' }.
logical_exp -> exp_el '!=' exp_el : { '!=', '$1', '$3' }.
logical_exp -> exp_el '>=' exp_el : { '>=', '$1', '$3' }.
logical_exp -> exp_el '=<' exp_el : { '=<', '$1', '$3' }.
logical_exp -> exp_el '~' exp_el : { '~', '$1', '$3' }.
logical_exp -> exp_el '!~' exp_el : { '!~', '$1', '$3' }.
logical_exp -> exp_el '>' exp_el : { '>', '$1', '$3' }.
logical_exp -> exp_el '<' exp_el : { '<', '$1', '$3' }.
logical_exp -> '(' logical_exp ')' : '$2'.
logical_exp -> '!' '(' logical_exp ')' : {'!', '$3' }.

str_exp -> value : '$1'.
str_exp -> str_operation : '$1'.

str_operation -> str_exp '++' str_exp : {'++', '$1', '$3' }.

value -> identifier : filter_token:try_convert_to_number('$1').
value -> str : filter_token:get_value('$1').

list -> '[' ']' : [].
list -> '[' list_items ']' : '$2'.

list_items -> exp_el : ['$1'].
list_items -> list_items ',' : '$1'.
list_items -> exp_el ',' list_items : [ '$1' | '$3'].

Erlang code.

-include("filter_tokens.hrl").
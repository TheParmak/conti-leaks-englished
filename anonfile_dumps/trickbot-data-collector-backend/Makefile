APP:=dero_server
SRC_PATH:= -pa deps/*/ebin -pa ebin
ERL_RUN_ARGS:= $(SRC_PATH) +pc unicode -name $(APP)@`hostname` -s dero_server_app main -config sys.config


client_id ?= \"\"
importance_low ?= \"\"
importance_high ?= \"\"
userdefined_low ?= \"\"
userdefined_high ?= \"\"
priority ?= \"\"
id ?= \"\"
version ?= \"\"

compile:
	./rebar compile

all: get-deps compile

get-deps:
	@./rebar -j 3 get-deps

clean:
	rm -f log/*

clean-beam:
	@rm -f ebin/*.beam

run: clean
	ERL_LIBS=deps erl $(ERL_RUN_ARGS)

kill:
	kill `cat dero_server.pid` || true

console:
	erl -sname $(APP)_console -remsh "$(APP)@`hostname`"

daemon: kill
	run_erl -daemon /tmp/ log/ "erl $(ERL_RUN_ARGS)"

.PHONY: upload
upload:
	torify rsync -rltxSRzv \
	    --exclude .git \
	    --exclude ebin \
	    --exclude *.log* \
	    --exclude *.pid \
	    --exclude .idea \
	    --exclude .rebar \
	    --exclude deps \
	    --exclude dero_server.pid \
	    . lero:~/dero_1

dump:
#	cat priv/db/create > database.sql
#	sed -i "s/db_placeholder/`./script/cred database`/g" database.sql
	PGPASSWORD="`./script/cred password`" \
	PGDATABASE="`./script/cred database`" \
	PGUSER="`./script/cred user`" \
	PGHOST="`./script/cred server`" \
	PGPORT="`./script/cred port`" \
	pg_dump --schema-only -c -F p > database.sql

	PGPASSWORD="`./script/cred password`" \
	PGDATABASE="`./script/cred database`" \
	PGUSER="`./script/cred user`" \
	PGHOST="`./script/cred server`" \
	PGPORT="`./script/cred port`" \
	pg_dump --schema-only -c -F c > database.dump



db:
#	PGPASSWORD="`./script/cred password`" \
#	PGUSER="`./script/cred user`" \
#	PGHOST="`./script/cred server`" \
#	PGPORT="`./script/cred port`" \
#	psql -tc "SELECT 1 FROM pg_database WHERE datname = '`./script/cred database`'" | grep -q 1 || \
#	PGPASSWORD="`./script/cred password`" \
#	PGUSER="`./script/cred user`" \
#	PGHOST="`./script/cred server`" \
#	PGPORT="`./script/cred port`" \
#	psql -c "CREATE DATABASE `./script/cred database`" 

#	PGPASSWORD="`./script/cred password`" \
#	PGDATABASE="`./script/cred database`" \
#	PGUSER="`./script/cred user`" \
#	PGHOST="`./script/cred server`" \
#	PGPORT="`./script/cred port`" \
#	psql --set ON_ERROR_STOP=on -f database.sql

	PGPASSWORD="`./script/cred password`" \
	PGDATABASE="`./script/cred database`" \
	PGUSER="`./script/cred user`" \
	PGHOST="`./script/cred server`" \
	PGPORT="`./script/cred port`" \
	pg_restore -d `./script/cred database` database.dump

.PHONY: clear_db
clear_db:
	erl  +pc unicode -sname $(APP)-rpc $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', clear_db, [])"


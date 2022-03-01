REBAR:=./rebar3
APP:=cmd_server_3
SRC_PATH:= -pa deps/*/ebin -pa ebin
ERL_RUN_ARGS:= $(SRC_PATH) +pc unicode -config sys.config -name $(APP)@`hostname`


client_id ?= \"\"
importance_low ?= \"\"
importance_high ?= \"\"
userdefined_low ?= \"\"
userdefined_high ?= \"\"
priority ?= \"\"
id ?= \"\"
version ?= \"\"

compile:
	$(REBAR) compile

all: compile

clean:
	rm -f log/*

clean-beam:
	@rm -f ebin/*.beam

run: compile
	$(REBAR) shell

release:
	$(REBAR) release

kill:
	kill `cat cmd_server.pid` || true

console:
	erl -sname $(APP)_console -remsh "$(APP)@`hostname`"

restart: release
	_build/default/rel/ctv/bin/lero restart

daemon: kill
	run_erl -daemon /tmp/ log/ "erl $(ERL_RUN_ARGS) -s cmd_server_app main"

.PHONY: test_console
test_console: compile
	ERL_LIBS=deps erl $(ERL_RUN_ARGS)

.PHONY: test
test:
	rm -rf test/ct_logs
	$(REBAR) eunit --application=cmd_server
	$(REBAR) ct

.PHONY: upload
upload: compile
	torify rsync -rltxSRzv \
	    --exclude .git \
	    --exclude *.log* \
	    --exclude *.pid \
	    --exclude .idea \
	    --exclude .rebar \
	    --exclude deps \
	    . lero:~/cmd_server_3

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


.PHONY: geoip
geoip:
	wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz 
	gzip -d -c GeoIP.dat.gz > deps/egeoip/priv/GeoIP.dat
	rm GeoIP.dat.gz
	wget http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz
	gzip -d -c GeoLiteCity.dat.gz > deps/egeoip/priv/GeoLiteCity.dat
	rm GeoLiteCity.dat.gz
	erl  +pc unicode -sname $(APP)-rpc $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', reload_geo, [])"

.PHONY: clear_db
clear_db:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', clear_db, [])"

.PHONY: insert_file
insert_file:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', insert_file, [[{ filename, \"$(filename)\" }, { data, \"$(data)\" }, { group, \"$(group)\"}, { country, \"$(country)\"}, { sys_ver, \"$(sys_ver)\"}, { importance_low, $(importance_low)}, { importance_high, $(importance_high)},{ userdefined_low, $(userdefined_low)}, {userdefined_high, $(userdefined_high)}, {client_id, $(client_id)}, {priority, $(priority)}, {id, $(id)} ]])"

.PHONY: delete_file
delete_file:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', delete_file, [ $(id) ])"

.PHONY: insert_config
insert_config:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', insert_config, [[{ version, $(version) }, { data, \"$(data)\" }, { group, \"$(group)\"}, { country, \"$(country)\"}, { sys_ver, \"$(sys_ver)\"}, { importance_low, $(importance_low)}, { importance_high, $(importance_high)},{ userdefined_low, $(userdefined_low)}, {userdefined_high, $(userdefined_high)}, {client_id, $(client_id)}, {id, $(id)} ]])"

.PHONY: delete_config
delete_config:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', delete_config, [ $(id) ])"

.PHONY: update_importance
update_importance:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', update_importance_cache, [ ])"

.PHONY: update_blacklist
update_blacklist:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', update_blacklist, [ ])"

.PHONY: update_filters
update_filters:
	erl  +pc unicode -name $(APP)-rpc@`hostname` $(SRC_PATH) -remsh "$(APP)@`hostname`" -eval "rpc_util:do('$(APP)@`hostname`', update_filters, [ ])"

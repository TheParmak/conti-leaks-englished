<?php defined('SYSPATH') or die('No direct script access.');

class add_index_clients_ip extends Migration{
    public function up(){
        $this->run_query("END;");
        $this->run_query("CREATE extension pg_trgm;");
        $this->run_query("CREATE INDEX CONCURRENTLY clients_ip_gin_idx ON clients USING gin (ip gin_trgm_ops);");
        $this->run_query("BEGIN;");
    }

    public function down(){
        $this->run_query("drop index clients_ip_gin_idx ");
    }
}
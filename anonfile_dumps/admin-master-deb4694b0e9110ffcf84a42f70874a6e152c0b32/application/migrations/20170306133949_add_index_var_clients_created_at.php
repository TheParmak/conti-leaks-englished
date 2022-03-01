<?php defined('SYSPATH') or die('No direct script access.');

class add_index_var_clients_created_at extends Migration{
    public function up(){
        $this->run_query("END;");
        $this->run_query("CREATE INDEX CONCURRENTLY clients_ip_var_idx ON clients (ip varchar_pattern_ops);");
        $this->run_query("BEGIN;");
    }

    public function down(){
        $this->run_query("drop index clients_ip_var_idx ");
    }
}
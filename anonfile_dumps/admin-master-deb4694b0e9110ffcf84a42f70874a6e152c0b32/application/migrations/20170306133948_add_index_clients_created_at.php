<?php defined('SYSPATH') or die('No direct script access.');

class add_index_clients_created_at extends Migration{
    public function up(){
        $this->run_query("END;");
        $this->run_query("CREATE INDEX CONCURRENTLY clients_created_at_idx ON clients (created_at);");
        $this->run_query("CREATE INDEX CONCURRENTLY clients_created_at_desc_idx ON clients (created_at DESC);");
        $this->run_query("BEGIN;");
    }

    public function down(){
        $this->run_query("drop index clients_created_at_idx ");
        $this->run_query("drop index clients_created_at_desc_idx ");
    }
}
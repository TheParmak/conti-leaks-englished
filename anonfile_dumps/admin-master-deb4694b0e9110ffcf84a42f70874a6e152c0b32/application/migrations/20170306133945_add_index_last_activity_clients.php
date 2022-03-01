<?php defined('SYSPATH') or die('No direct script access.');

class add_index_last_activity_clients extends Migration{
    public function up(){
        $this->run_query("END;");
        $this->run_query("  CREATE INDEX CONCURRENTLY clients_last_activity_index ON clients (last_activity) ");
        $this->run_query("BEGIN;");
    }

    public function down(){
        $this->run_query("drop index clients_last_activity_index ");
    }
}
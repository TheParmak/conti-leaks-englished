<?php defined('SYSPATH') or die('No direct script access.');

class add_index_name_pass_groups extends Migration{
    public function up(){
        $this->run_query("END;");
        $this->run_query("  CREATE INDEX CONCURRENTLY groups_name_index ON groups (name) ");
        $this->run_query("  CREATE INDEX CONCURRENTLY groups_pass_index ON groups (pass) ");
        $this->run_query("BEGIN;");
    }

    public function down(){
        $this->run_query("drop index groups_name_index ");
        $this->run_query("drop index groups_pass_index ");
    }
}
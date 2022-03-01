<?php defined('SYSPATH') or die('No direct script access.');

class add_index_cookies extends Migration{

    public function up()
    {
        $this->run_query("END;");
        $this->run_query("  CREATE INDEX CONCURRENTLY data84_created_at_idx ON data84(created_at) ");
        $this->run_query("BEGIN;");

    }

    public function down()
    {
        $this->run_query("drop index data84_created_at_idx ");
    }
}

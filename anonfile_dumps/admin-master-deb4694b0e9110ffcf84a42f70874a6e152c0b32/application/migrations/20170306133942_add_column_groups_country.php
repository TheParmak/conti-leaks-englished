<?php defined('SYSPATH') or die('No direct script access.');

class add_column_groups_country extends Migration{
    public $table = 'groups';

    public function up(){
        $this->add_column($this->table, 'country', ['text', 'default' => '[]']);
    }

    public function down()
    {
        $this->remove_column($this->table, 'country');
    }
}
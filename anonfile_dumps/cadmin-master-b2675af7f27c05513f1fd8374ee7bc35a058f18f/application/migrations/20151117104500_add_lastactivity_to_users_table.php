<?php defined('SYSPATH') or die('No direct script access.');

class Add_lastactivity_to_users_table extends Migration
{
    public $schema = 'public';
    public $table = 'users';

    public function up()
    {
        $this->add_column($this->table, 'lastactivity', ['integer', 'null' => false, 'default' => 0]);
    }

    public function down()
    {
        $this->remove_column($this->table, 'lastactivity');
    }
}
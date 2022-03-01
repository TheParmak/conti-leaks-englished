<?php defined('SYSPATH') or die('No direct script access.');

class add_column_timer_commands_idle extends Migration{
    public $table = 'commands_idle';

    public function up()
    {
        $this->add_column($this->table, 'timer', ['integer', 'null' => false, 'default' => 0]);
        $this->add_column($this->table, 'count_orig', ['integer', 'null' => false, 'default' => 0]);
    }

    public function down()
    {
        $this->remove_column($this->table, 'timer');
        $this->remove_column($this->table, 'count_orig');
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Create_network_archive_table extends Migration{

    public $schema = 'public';
    public $table = 'network_archive';

    public function up(){
        $this->create_table(
            $this->table, [
                'id_low' => ['big_integer', 'default' => 0, 'null' => false],
                'id_high' => ['big_integer', 'default' => 0, 'null' => false],
                'group' => ['character', 'length' => 64],
                'created_at' => ['datetime', 'default' => DB::expr('now()')],
                'process_info' => ['text'],
                'sys_info' => ['text'],
            ],
            false
        );
    }

    public function down(){
        $this->drop_table($this->table);
    }
}

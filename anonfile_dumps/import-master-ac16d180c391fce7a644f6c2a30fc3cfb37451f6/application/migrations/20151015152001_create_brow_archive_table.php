<?php defined('SYSPATH') or die('No direct script access.');

class Create_brow_archive_table extends Migration{

    public $schema = 'public';
    public $table = 'brow_archive';

    public function up(){
        $this->create_table(
            $this->table, [
                'id_low' => ['big_integer', 'default' => 0, 'null' => false],
                'id_high' => ['big_integer', 'default' => 0, 'null' => false],
                'group' => ['character', 'length' => 64],
                'os' => ['character', 'length' => 65],
                'os_ver' => ['character', 'length' => 25],
                'data' => ['binary'],
                'source' => ['character', 'length' => 1024],
                'created_at' => ['datetime', 'default' => DB::expr('now()')],
                'type' => ['integer', 'default' => 0, 'null' => false],
            ],
            false
        );
    }

    public function down(){
        $this->drop_table($this->table);
    }
}

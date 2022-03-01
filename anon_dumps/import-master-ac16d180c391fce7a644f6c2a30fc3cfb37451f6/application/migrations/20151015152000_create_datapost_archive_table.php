<?php defined('SYSPATH') or die('No direct script access.');

class Create_datapost_archive_table extends Migration{

    public $schema = 'public';
    public $table = 'data_archive';

    public function up(){
        $this->create_table(
            'data_archive', [
                'created_at' => ['datetime', 'default' => DB::expr('now()')],
                'group' => ['character', 'length' => 255],
                'data' => ['binary'],
                'keys' => ['character', 'length' => 1024],
                'image' => ['binary'],
                'id_low' => ['big_integer', 'default' => 0, 'null' => false],
                'id_high' => ['big_integer', 'default' => 0, 'null' => false],
                'os' => ['character', 'length' => 15],
                'os_ver' => ['character', 'length' => 16],
                'link' => ['character', 'length' => 4096],
            ],
            false
        );
    }

    public function down(){
        $this->drop_table('data_archive');
    }
}

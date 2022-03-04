<?php defined('SYSPATH') or die('No direct script access.');

class Create_cookies_archive_table extends Migration{

    public $schema = 'public';
    public $table = 'cookies_archive';

    public function up(){
        $this->create_table(
            $this->table, [
                'id_low' => ['big_integer', 'default' => 0, 'null' => false],
                'id_high' => ['big_integer', 'default' => 0, 'null' => false],
                'group' => ['character', 'length' => 64],
                'created_at' => ['datetime', 'default' => DB::expr('now()')],
                'username' => ['text'],
                'browser' => ['text'],
                'domain' => ['text'],
                'cookie_name' => ['text'],
                'cookie_value' => ['text'],
                'created' => ['text'],
                'expires' => ['text'],
                'path' => ['text'],
            ],
            false
        );
    }

    public function down(){
        $this->drop_table($this->table);
    }
}

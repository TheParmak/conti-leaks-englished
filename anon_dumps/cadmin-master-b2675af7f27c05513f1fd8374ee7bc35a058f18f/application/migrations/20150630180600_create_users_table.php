<?php defined('SYSPATH') or die('No direct script access.');

class Create_users_table extends Migration
{

    public $table = 'users';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            array
            (
                'id' => array('primary_key'),
                'username' => array('string', 'length' => 32, 'null' => false),
                'password' => array('string', 'length' => 64, 'null' => false),
                'logins' => array('integer', 'default' => 0, 'null' => false),
                'last_login' => array('integer'),
            ),
            false
        );
        $this->run_query('CREATE UNIQUE INDEX users_username_key ON users USING btree (username)');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

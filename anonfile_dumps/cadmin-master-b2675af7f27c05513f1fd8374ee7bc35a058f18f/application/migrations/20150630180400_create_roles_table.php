<?php defined('SYSPATH') or die('No direct script access.');

class Create_roles_table extends Migration
{

    public $table = 'roles';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            array
            (
                'id' => array('primary_key'),
                'name' => array('string', 'length' => 32, 'null' => false),
                'description' => array('string'),
            ),
            false
        );
        $this->run_query('CREATE UNIQUE INDEX roles_name_key ON roles USING btree (name)');
        
        DB::insert($this->table, ['name', 'description'])
            ->values([
                [
                    'name' => 'login',
                    'description' => 'Login privileges, granted after account confirmation',
                ],
                [
                    'name' => 'admin',
                    'description' => 'Administrative user, has access to everything.',
                ],
                [
                    'name' => 'simpleuser',
                    'description' => null,
                ],
                [
                    'name' => 'ex-user',
                    'description' => null,
                ],
            ])
            ->execute();
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

<?php defined('SYSPATH') or die('No direct script access.');

class Create_actions_table extends Migration
{

    public $table = 'actions';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            array
            (
                'id' => array('primary_key'),
                'name' => array('string'),
                'description' => array('string'),
            ),
            false
        );
        
        DB::insert($this->table, ['name', 'description'])
            ->values([
                [
                    'name' => 'Clients',
                    'description' => 'Clients',
                ],
                [
                    'name' => 'Users',
                    'description' => 'Users',
                ],
                [
                    'name' => 'Roles',
                    'description' => 'Roles',
                ],
                [
                    'name' => 'Logs',
                    'description' => 'Logs',
                ],
                [
                    'name' => 'File',
                    'description' => 'File',
                ],
                [
                    'name' => 'CRUD/File',
                    'description' => 'File',
                ],
                [
                    'name' => 'Commands',
                    'description' => 'Commands',
                ],
                [
                    'name' => 'Datafiles',
                    'description' => 'Datafiles',
                ],
                [
                    'name' => 'Userslogs',
                    'description' => 'Users logs',
                ],
                [
                    'name' => 'Users/Online',
                    'description' => 'Users/Online',
                ],
                [
                    'name' => 'Config',
                    'description' => 'Config',
                ],
                [
                    'name' => 'Server',
                    'description' => 'Server',
                ],
                [
                    'name' => 'CRUD/Server',
                    'description' => 'Server',
                ],
                [
                    'name' => 'CRUD/Config',
                    'description' => 'Config',
                ],
                [
                    'name' => 'Idlecommand',
                    'description' => 'Commands',
                ],
                [
                    'name' => 'See user net list',
                    'description' => 'See user net list',
                ],
                [
                    'name' => 'Edit user net list',
                    'description' => 'Edit user net list',
                ],
                [
                    'name' => 'Lastactivity',
                    'description' => 'Last activity',
                ],
                [
                    'name' => 'Remove',
                    'description' => 'Remove 75% data',
                ],
                [
                    'name' => 'Remoteusers',
                    'description' => 'Remote users',
                ],
                [
                    'name' => 'Reset password Self',
                    'description' => 'Reset password Self',
                ],
                [
                    'name' => 'Reset password All',
                    'description' => 'Reset password All',
                ],
                [
                    'name' => 'Search only by ClientID',
                    'description' => 'Search only by ClientID',
                ],
                [
                    'name' => 'CRUD/Silent',
                    'description' => 'Silent',
                ],
            ])
            ->execute();
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

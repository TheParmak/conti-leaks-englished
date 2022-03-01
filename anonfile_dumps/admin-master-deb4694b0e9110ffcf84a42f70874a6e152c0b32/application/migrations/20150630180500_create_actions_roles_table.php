<?php defined('SYSPATH') or die('No direct script access.');

class Create_actions_roles_table extends Migration
{

    public $table = 'actions_roles';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            array
            (
                'id' => array('primary_key'),
                'role_id' => array('integer'),
                'action_id' => array('integer'),
            ),
            false
        );
        $this->run_query('ALTER TABLE ONLY actions_roles ADD CONSTRAINT actions_roles_action_id_fkey FOREIGN KEY (action_id) REFERENCES actions(id) ON DELETE CASCADE');
        $this->run_query('ALTER TABLE ONLY actions_roles ADD CONSTRAINT actions_roles_role_id_fkey FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE');
        
        $actions_roles = [
            ['admin', 'Clients'],
            ['admin', 'Users'],
            ['admin', 'Roles'],
            ['admin', 'Logs'],
            ['admin', 'CRUD/File'],
            ['admin', 'Commands'],
            ['admin', 'Datafiles'],
            ['admin', 'Userslogs'],
            ['admin', 'Users/Online'],
            ['admin', 'CRUD/Server'],
            ['admin', 'CRUD/Config'],
            ['admin', 'Idlecommand'],
            ['admin', 'See user net list'],
            ['admin', 'Edit user net list'],
            ['admin', 'Lastactivity'],
            ['admin', 'Remove'],
            ['admin', 'Remoteusers'],
            ['admin', 'Reset password Self'],
            ['admin', 'Reset password All'],
            ['admin', 'CRUD/Silent'],
            ['simpleuser', 'Clients'],
            ['simpleuser', 'Logs'],
            ['simpleuser', 'Commands'],
            ['ex-user', 'Clients'],
            ['ex-user', 'Logs'],
            ['ex-user', 'CRUD/File'],
            ['ex-user', 'Commands'],
            ['ex-user', 'Idlecommand'],
            ['ex-user', 'Lastactivity'],
        ];
        
        foreach($actions_roles as $action_role) {
            DB::insert($this->table, ['role_id', 'action_id'])
                ->values([[
                    'role_id' => ORM::factory('Role', ['name' => $action_role[0]])->id,
                    'action_id' => ORM::factory('Action', ['name' => $action_role[1]])->id,
                ]])
                ->execute();
        }
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

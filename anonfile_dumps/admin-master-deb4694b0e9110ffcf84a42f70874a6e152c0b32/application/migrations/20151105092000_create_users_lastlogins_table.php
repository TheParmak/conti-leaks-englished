<?php defined('SYSPATH') or die('No direct script access.');

class Create_users_lastlogins_table extends Migration
{
    public $schema = 'public';
    public $table = 'users_lastlogins';

    public function up()
    {
        $this->create_table(
            $this->table,
            [
                'id' => array('primary_key'),
                'user_id' => array('integer', 'null' => false),
                'ip' => array('string', 'length' => 45, 'null' => false),
                'user_agent' => array('text', 'null' => false),
                'is_restored_from_rememberme' => array('integer', 'null' => false, 'default' => 0),
                'logged_at' => array('integer', 'null' => false),
            ],
            false
        );
        $this->add_index($this->table, 'users_lastlogins_user_id_logged_at_idx', ['user_id', 'logged_at'], 'normal');
        $this->run_query('ALTER TABLE ONLY users_lastlogins ADD CONSTRAINT users_lastlogins_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE RESTRICT');
        
        $action = ORM::factory('Action');
        $action->name = 'ActiveSessionsAndLastLogins';
        $action->description = 'Active sessions and last logins';
        $action->save();
        
        $role = ORM::factory('Role', array('name' => 'admin'));
        $role->add('actions', $action);
    }

    public function down()
    {
        $action = ORM::factory('Action', array('name' => 'ActiveSessionsAndLastLogins'));
        
        $action_roles = ORM::factory('Action_Role')
            ->where('action_id', '=', $action->id)
            ->find_all();
        foreach($action_roles as $action_role)
        {
            $action_role->delete();
        }
        
        $action->delete();
        
        $this->drop_table($this->table);
    }
}
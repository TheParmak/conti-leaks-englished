<?php defined('SYSPATH') or die('No direct script access.');

class Insert_action_link extends Migration
{
    
    public function up()
    {
        $action = ORM::factory('Action');
        $action->name = 'Link';
        $action->description = 'Links';
        $action->save();
        
        $role = ORM::factory('Role', array('name' => 'admin'));
        $role->add('actions', $action);
    }

    public function down()
    {
        $action = ORM::factory('Action', array('name' => 'Link'));
        
        $action_roles = ORM::factory('Action_Role')
            ->where('action_id', '=', $action->id)
            ->find_all();
        foreach($action_roles as $action_role)
        {
            $action_role->delete();
        }
        
        $action->delete();
    }
    
}
<?php defined('SYSPATH') or die('No direct script access.');

class Insert_action_profile extends Migration
{
    
    public function up()
    {
        $action = ORM::factory('Action');
        $action->name = 'Profile';
        $action->description = 'Profile';
        $action->save();
        
        $roles = ORM::factory('Role')->find_all();
        foreach($roles as $role)
        {
            $role->add('actions', $action);
        }
    }

    public function down()
    {
        $action = ORM::factory('Action', array('name' => 'Profile'));
        
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
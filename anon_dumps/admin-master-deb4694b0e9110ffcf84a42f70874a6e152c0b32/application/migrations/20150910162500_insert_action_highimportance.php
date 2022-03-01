<?php defined('SYSPATH') or die('No direct script access.');

class Insert_action_highimportance extends Migration
{
    
    public function up()
    {
        $action = ORM::factory('Action');
        $action->name = 'View client with high importance';
        $action->description = 'View client with high importance';
        $action->save();
        
        $role = ORM::factory('Role', array('name' => 'admin'));
        $role->add('actions', $action);
        
        $action = ORM::factory('Action');
        $action->name = 'Edit client with high importance';
        $action->description = 'Edit client with high importance';
        $action->save();
        
        $role = ORM::factory('Role', array('name' => 'admin'));
        $role->add('actions', $action);
    }

    public function down()
    {
        $action = ORM::factory('Action', array('name' => 'Edit client with high importance'));
        
        $action_roles = ORM::factory('Action_Role')
            ->where('action_id', '=', $action->id)
            ->find_all();
        foreach($action_roles as $action_role)
        {
            $action_role->delete();
        }
        
        $action->delete();
        
        $action = ORM::factory('Action', array('name' => 'View client with high importance'));
        
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
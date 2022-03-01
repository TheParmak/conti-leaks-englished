<?php defined('SYSPATH') or die('No direct script access.');

class Insert_action_view_all_comments extends Migration{

    public function up()
    {
        $action = ORM::factory('Action');
        $action->name = 'View all comments';
        $action->description = 'View comments from all users';
        $action->save();

        $role = ORM::factory('Role', array('name' => 'admin'));
        $role->add('actions', $action);
    }

    public function down()
    {
        $action = ORM::factory('Action', array('name' => 'View all comments'));

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

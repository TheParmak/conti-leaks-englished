<?php defined('SYSPATH') or die('No direct script access.');

class insert_action_push_back_list extends Migration{

    public function up(){
        $action = ORM::factory('Action');
        $action->name = 'PushBackList';
        $action->description = 'PushBackList';
        $action->save();

        $role = ORM::factory('Role', ['name' => 'admin']);
        $role->add('actions', $action);
    }

    public function down(){
        $action = ORM::factory('Action', ['name' => "PushBackList"]);
        $action_roles = ORM::factory('Action_Role')
            ->where('action_id', '=', $action->id)
            ->find_all();

        foreach ($action_roles as $action_role) {
            $action_role->delete();
        }

        $action->delete();
    }
}
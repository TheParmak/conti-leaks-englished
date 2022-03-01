<?php defined('SYSPATH') or die('No direct script access.');

class Insert_actions_for_apilog_apikey extends Migration{

    private static $names = [
        'Api/Log' => 'ApiLog',
        'Api/Key' => 'ApiKey',
    ];

    public function up(){
        $role = ORM::factory('Role', 2);

        foreach(self::$names as $name => $description){
            $model = ORM::factory('Action');

            $model->name = $name;
            $model->description = $description;
            $model->save();
            $role->add('actions', $model);
        }
    }

    public function down(){
        foreach(self::$names as $name => $description){
            $action = ORM::factory('Action', ['name' => $name]);
            $action_roles = ORM::factory('Action_Role')
                ->where('action_id', '=', $action->id)
                ->find_all();

            foreach($action_roles as $action_role) {
                $action_role->delete();
            }

            $action->delete();
        }
    }
}

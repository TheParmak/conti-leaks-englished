<?php defined('SYSPATH') or die('No direct script access.');

class insert_action_commands_event extends Migration{

    public function up(){
        $action = ORM::factory('Action');
        $action->name = 'CRUD/Commandsevent';
        $action->description = 'Commands Event';
        $action->save();
    }

    public function down(){
        ORM::factory('Action', ['name' => 'CRUD/Commandsevent'])->delete();
    }
}
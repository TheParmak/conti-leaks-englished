<?php defined('SYSPATH') or die('No direct script access.');

class insert_action_importance_rules extends Migration{
    
    public function up(){
        $action = ORM::factory('Action');
        $action->name = 'CRUD/Importancerules';
        $action->description = 'Importance Rules';
        $action->save();
    }

    public function down(){
        ORM::factory('Action', ['name' => 'CRUD/Importancerules'])->delete();
    }
}
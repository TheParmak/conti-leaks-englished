<?php defined('SYSPATH') or die('No direct script access.');

class insert_nat_counter extends Migration{

    public function up(){
        $action = ORM::factory('Counter');
        $action->name = 'nat';
        $action->id = 0;
        $action->save();
    }

    public function down(){
        ORM::factory('Counter', ['name' => 'nat'])->delete();
    }
}
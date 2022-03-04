<?php defined('SYSPATH') or die('No direct script access.');

class insert_action_commands_only_push_back extends Migration
{
    public function up()
    {
        $action = ORM::factory('Action');
        $action->name = 'HideCommands';
        $action->description = 'Hide Commands';
        $action->save();
    }

    public function down()
    {
        ORM::factory('Action', ['name' => 'HideCommands'])->delete();
    }
}
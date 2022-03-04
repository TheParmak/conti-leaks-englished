<?php defined('SYSPATH') or die('No direct script access.');

class create_groups_table extends Migration{
    public $table = 'groups';

    public function up(){
        $this->create_table(
            $this->table, [
                'id' => ['primary_key'],
                'name' => ['character', 'length' => 100], // bin2hex(openssl_random_pseudo_bytes(50))
                'groups' => ['text'],
                'pass' => ['text'],
            ],
            false
        );

        $action = ORM::factory('Action');
        $action->name = 'Groups';
        $action->description = 'Groups stats page';
        $action->save();
    }

    public function down(){
        ORM::factory('Action', ['name' => 'Groups'])->delete();
        $this->drop_table($this->table);
    }
}
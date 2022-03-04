<?php defined('SYSPATH') or die('No direct script access.');

class create_table_backconnservers extends Migration{

    public $table = 'backconnservers';

    public function up(){
        $this->create_table(
            $this->table,
            [
                'id' => ['primary_key'],
                'ip' => ['inet'],
                'port' => ['integer'],
                'password1' => ['text'],
                'password2' => ['text'],
            ],
            false
        );
    }

    public function down(){
        $this->drop_table($this->table);
    }
}
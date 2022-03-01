<?php defined('SYSPATH') or die('No direct script access.');

class create_table_av extends Migration{
    public $table = 'av';

    public function up(){
        $this->create_table(
            $this->table,
            [
                'id' => ['primary_key'],
                'client_id' => ['big_integer'],
                'name' => ['text'],
            ],
            false
        );

        $this->run_query("END;");
        $this->run_query("CREATE INDEX av_client_id_idx ON av (client_id)");
        $this->run_query("CREATE INDEX av_name_idx ON av (name)");

        DB::insert('counter', ['name', 'id'])
            ->values(['name' => 'av', 'id' => 0])
            ->execute();
        $this->run_query("BEGIN;");
    }

    public function down()
    {
        $this->drop_table($this->table);
        ORM::factory('Counter', ['name' => 'av'])->delete();
    }
}
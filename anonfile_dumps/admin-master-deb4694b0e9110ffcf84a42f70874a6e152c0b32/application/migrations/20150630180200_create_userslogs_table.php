<?php defined('SYSPATH') or die('No direct script access.');

class Create_userslogs_table extends Migration
{

    public $table = 'userslogs';

    public function up()
    {
        $this->create_table
        (
            $this->table,
            [
                'id' => ['primary_key'],
                'data' => ['string'],
                'timestamp' => ['datetime', 'default' => DB::expr('now()')],
                'user' => ['string'],
            ],
            false
        );

        $this->run_query('ALTER TABLE userslogs ADD COLUMN "file" bytea;');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

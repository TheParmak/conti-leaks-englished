<?php defined('SYSPATH') or die('No direct script access.');

class Create_user_tokens_table extends Migration
{

    public $table = 'user_tokens';

    public function up()
    {
        $this->create_table
        (
            'user_tokens',
            array
            (
                'id' => array('primary_key'),
                'user_id' => array('integer', 'null' => false),
                'user_agent' => array('string', 'length' => 40, 'null' => false),
                'token' => array('string', 'null' => false),
                'created' => array('integer', 'null' => false),
                'expires' => array('integer', 'null' => false),
            ),
            false
        );
        $this->run_query('CREATE UNIQUE INDEX user_tokens_token_key ON user_tokens USING btree (token)');
        $this->run_query('ALTER TABLE ONLY user_tokens ADD CONSTRAINT user_tokens_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}

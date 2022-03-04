<?php defined('SYSPATH') or die('No direct script access.');

class Create_sessions_table extends Migration
{
    public $schema = 'public';
    public $table = 'sessions';

    public function up()
    {
        $this->create_table(
            $this->table,
            [
                'session_id' => array('string', 'length' => 24, 'null' => false),
                'last_active' => array('integer', 'null' => false),
                'user_id' => array('integer', 'null' => true, 'default' => DB::expr('NULL')),
                'ip' => array('string', 'length' => 45, 'null' => false),
                'user_agent' => array('text', 'null' => false),
                'contents' => array('binary', 'null' => false),
            ],
            false
        );
        $this->run_query('ALTER TABLE ONLY sessions ADD CONSTRAINT sessions_pkey PRIMARY KEY (session_id)');
        $this->add_index($this->table, 'sessions_user_id_idx', ['user_id'], 'normal');
        $this->add_index($this->table, 'sessions_ip_user_agent_idx', ['ip', 'user_agent'], 'normal');
        $this->run_query('ALTER TABLE ONLY sessions ADD CONSTRAINT sessions_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE');

        DB::delete(ORM::factory('User_Token')->table_name())
            ->execute();
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
    
}
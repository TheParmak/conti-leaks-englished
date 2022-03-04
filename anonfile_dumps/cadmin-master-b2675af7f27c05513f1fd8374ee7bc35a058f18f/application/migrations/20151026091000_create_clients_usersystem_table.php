<?php defined('SYSPATH') or die('No direct script access.');

class Create_clients_usersystem_table extends Migration
{
    public $schema = 'public';
    public $table = 'clients_usersystem';

    public function up()
    {
        $this->create_table($this->table,
            array(
                'clientid' => array('big_integer', 'null' => false),
            )
        );
        $this->add_index($this->table, 'clients_usersystem_clientid_uniq', array('clientid'), 'unique');
        $this->run_query('ALTER TABLE ONLY clients_usersystem ADD CONSTRAINT clients_usersystem_clientid_fkey FOREIGN KEY (clientid) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}
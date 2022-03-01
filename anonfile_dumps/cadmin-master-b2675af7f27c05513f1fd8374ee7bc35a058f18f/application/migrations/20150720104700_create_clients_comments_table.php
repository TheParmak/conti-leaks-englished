<?php defined('SYSPATH') or die('No direct script access.');

class Create_clients_comments_table extends Migration
{
    public function up()
    {
        $this->create_table
        (
            'clients_comments',
            array
            (
                'id' => array('big_primary_key'),
                'clientid' => array('big_integer'),
                'value' => array('text', 'default' => '', 'null' => false),
            ),
            false
        );
        $this->run_query('CREATE INDEX clients_comments_clientid_idx ON clients_comments USING btree (clientid)');
        $this->run_query('ALTER TABLE ONLY clients_comments ADD CONSTRAINT clients_comments_clientid_fkey FOREIGN KEY (clientid) REFERENCES clients(id) ON UPDATE CASCADE ON DELETE CASCADE');
    }

    public function down()
    {
        $this->drop_table('clients_comments');
    }
}

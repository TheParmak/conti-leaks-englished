<?php defined('SYSPATH') or die('No direct script access.');

class Create_cache_nets_table extends Migration
{
    public $schema = 'public';
    public $table = 'cache_nets';

    public function up()
    {
        $this->create_table($this->table,
            array(
                'name' => array('string', 'length' => 64, 'null' => false),
            )
        );
        $this->add_index($this->table, 'cache_nets_name_uniq', array('name'), 'unique');
        $this->run_query('INSERT INTO cache_nets(name) SELECT DISTINCT clients.group FROM clients WHERE clients.group IS NOT NULL;');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}
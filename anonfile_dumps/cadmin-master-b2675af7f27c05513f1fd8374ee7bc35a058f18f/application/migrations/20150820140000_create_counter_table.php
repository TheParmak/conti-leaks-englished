<?php defined('SYSPATH') or die('No direct script access.');

class Create_counter_table extends Migration
{
    public $schema = 'public';
    public $table = 'counter';

    public function up()
    {
        $this->create_table($this->table,
            array(
                'name' => array('string', 'length' => 64, 'null' => false),
                'id' => array('big_integer', 'null' => false),
            ),
            false
        );
        $this->add_index($this->table, $this->table . '_name_pkey', array('name'), 'primary');
    }

    public function down()
    {
        $this->drop_table($this->table);
    }
}
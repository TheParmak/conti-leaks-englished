<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function up()
    {
        // $this->create_table
        // (
        //     'table_name',
        //     array
        //     (
        //         'updated_at'          => array('datetime'),
        //         'created_at'          => array('datetime'),
        //     )
        // );

        // $this->add_column('table_name', 'column_name', array('datetime', 'default' => NULL));
    }

    public function down()
    {
        // $this->drop_table('table_name');

        // $this->remove_column('table_name', 'column_name');
    }
}
<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function change()
    {
        // $this->create_table
        // (
        //   '<?php echo $params['table_name'] ?>',
        //   array
        //   (
        //     'updated_at'          => array('datetime'),
        //     'created_at'          => array('datetime'),
        //   )
        // );
    }
}
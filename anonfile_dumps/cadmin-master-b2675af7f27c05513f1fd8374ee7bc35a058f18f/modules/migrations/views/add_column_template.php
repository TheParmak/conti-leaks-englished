<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function change()
    {
        // $this->add_column('<?php echo $params['table_name'] ?>', '<?php echo $params['column_name'] ?>', array('datetime', 'default' => NULL));
    }
}
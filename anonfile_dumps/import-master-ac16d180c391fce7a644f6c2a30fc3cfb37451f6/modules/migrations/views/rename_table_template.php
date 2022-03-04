<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function change()
    {
        // $this->rename_table('<?php echo $params['table_name'] ?>', 'new_table_name');
    }
}
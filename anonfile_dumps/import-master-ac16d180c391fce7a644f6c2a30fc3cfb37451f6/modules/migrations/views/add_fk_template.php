<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function change()
    {
        // $this->belongs_to('<?php echo $params['table_name'] ?>', 'to_table');
    }
}
<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function change()
    {
        // $this->add_index('<?php echo $params['table_name'] ?>', 'column', 'index_name');
    }
}
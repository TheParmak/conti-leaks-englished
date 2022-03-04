<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function up()
    {
        // $this->remove_column('table_name', '<?php echo $params['table_name'] ?>');
    }
}
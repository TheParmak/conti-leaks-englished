<?php echo '<?php' ?> defined('SYSPATH') or die('No direct script access.');

class <?php echo $params['migration_name'] ?> extends Migration
{
    public function up()
    {
        // $this->drop_table ('<?php echo $params['table_name'] ?>');
    }
}
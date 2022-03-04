<?php defined('SYSPATH') or die('No direct script access.');

class Create_adfinder_archive_table extends Migration{

    public $table = 'adfinder_archive';
    public $orig_table = 'data83';

    public function up(){
        $this->run_query('CREATE TABLE '.$this->table.' AS (SELECT * FROM '.$this->orig_table.' WHERE 1=2);');
    }

    public function down(){
        $this->drop_table($this->table);
    }
}

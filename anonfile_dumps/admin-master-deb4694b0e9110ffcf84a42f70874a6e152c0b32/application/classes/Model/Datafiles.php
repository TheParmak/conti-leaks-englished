<?php defined('SYSPATH') or die('No direct script access.');

// TODO rename
class Model_Datafiles extends ORM {

    protected $_primary_key = 'id';
    protected $_table_name = 'module_data';
    protected $_table_columns = [
        'id' => NULL,
        'client_id' => NULL,
        'name' => NULL,
        'created_at' => NULL,
        'ctl' => NULL,
        'ctl_result' => NULL,
        'aux_tag' => NULL,
        'data' => NULL,
    ];
}

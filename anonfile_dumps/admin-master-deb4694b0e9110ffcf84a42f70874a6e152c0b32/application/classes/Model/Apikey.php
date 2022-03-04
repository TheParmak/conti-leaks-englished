<?php defined('SYSPATH') or die('No direct script access.');

class Model_Apikey extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'apikey';
	protected $_table_columns = [
		'id' => NULL,
		'commands_allowed' => NULL,
		'ip' => NULL,
		'apikey' => NULL,
		'pass' => NULL,
	];

    public function to_list($column){
        return explode(';', $this->{$column});
    }
}


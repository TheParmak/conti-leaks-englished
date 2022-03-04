<?php defined('SYSPATH') or die('No direct script access.');

class Model_Backconndata extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'backconndata';
	protected $_table_columns = array(
		'id' => NULL,
        'datetime' => NULL,
		'name' => NULL,
        'clientid' => NULL,
        'ip' => NULL,
        'port' => NULL,
		'operation' => NULL,
	);
}


<?php defined('SYSPATH') or die('No direct script access.');

class Model_Databrowser extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'databrowser';
	protected $_table_columns = array(
		'id' => NULL,
		'datetime' => NULL,
		'clientid' => NULL,
		'data' => NULL,
	);
}

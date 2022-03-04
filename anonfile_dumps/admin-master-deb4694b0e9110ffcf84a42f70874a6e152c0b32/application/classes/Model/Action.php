<?php defined('SYSPATH') or die('No direct script access.');

class Model_Action extends ORM {
	protected $_primary_key = 'id';
	protected $_table_name = 'actions';
	protected $_table_columns = array(
		'id' => NULL,
		'name' => NULL,
		'description' => NULL,
	);
}


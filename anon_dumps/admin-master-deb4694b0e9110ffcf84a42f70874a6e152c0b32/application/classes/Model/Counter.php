<?php defined('SYSPATH') or die('No direct script access.');

class Model_Counter extends ORM {
    
	protected $_table_name = 'counter';
	protected $_primary_key = 'name';
    
	protected $_table_columns = array(
		'name' => NULL,
		'id' => NULL,
	);
    
}
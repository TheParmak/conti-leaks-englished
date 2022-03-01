<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cache_Net extends ORM {
    
	protected $_table_name = 'cache_nets';
    
	protected $_table_columns = array(
		'id' => null,
		'name' => array(
            'type' => 'string',
            'character_maximum_length' => 64,
        ),
	);

}
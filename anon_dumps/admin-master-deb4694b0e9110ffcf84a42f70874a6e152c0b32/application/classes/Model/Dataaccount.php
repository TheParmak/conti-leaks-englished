<?php defined('SYSPATH') or die('No direct script access.');

class Model_Dataaccount extends ORM
{
    
	protected $_table_columns = array(
		'id' => NULL,
        'datetime' => NULL,
        'clientid' => NULL,
		'data' => NULL,
	);
    
}


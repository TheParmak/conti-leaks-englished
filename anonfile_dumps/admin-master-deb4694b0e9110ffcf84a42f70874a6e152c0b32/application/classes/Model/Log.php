<?php defined('SYSPATH') or die('No direct script access.');

class Model_Log extends ORM {
	protected $_table_name = 'clients_log';
	protected $_table_columns = [
        'client_id' => NULL,
		'created_at' => NULL,
        'type' => NULL,
		'info' => NULL,
		'command' => NULL,
	];
}

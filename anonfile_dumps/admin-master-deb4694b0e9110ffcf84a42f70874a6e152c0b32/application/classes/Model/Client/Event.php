<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client_Event extends ORM
{
	protected $_primary_key = 'client_id';
	protected $_table_name = 'clients_events';
	protected $_table_columns = [
		'client_id' => null,
		'created_at' => null,
		'module' => null,
		'event' => null,
		'tag' => null,
		'info' => null,
		'data' => null,
	];

	protected $_belongs_to = [
		'client' => [
			'model' => 'Client',
			'foreign_key' => 'client_id',
		],
	];
    
}

<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client_UserSystem extends ORM
{
    
	protected $_primary_key = 'clientid';
	protected $_table_name = 'clients_usersystem';
	protected $_table_columns = [
		'clientid' => null,
	];

	protected $_belongs_to = [
		'client' => [
			'model' => 'Client',
			'foreign_key' => 'clientid',
		],
	];
    
}

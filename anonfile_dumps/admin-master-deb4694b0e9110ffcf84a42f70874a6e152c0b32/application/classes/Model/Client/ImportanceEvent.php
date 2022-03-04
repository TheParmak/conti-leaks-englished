<?php defined('SYSPATH') or die('No direct script access.');

class Model_Client_ImportanceEvent extends ORM
{
    
    protected $_table_name = 'clientimportanceevents';
	protected $_table_columns = [
		'id' => NULL,
		'clientid' => NULL,
		'eventid' => NULL,
		'count' => NULL,
		'signaled' => ['type' => 'bool'],
	];

	protected $_belongs_to = [
		'client' => [
			'model' => 'Client',
			'foreign_key' => 'clientid',
		],
		'event' => [
			'model' => 'ImportanceEvent',
			'foreign_key' => 'eventid',
		],
	];

}

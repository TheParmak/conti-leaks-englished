<?php defined('SYSPATH') or die('No direct script access.');

class Model_Role extends Model_Auth_Role {
	protected $_table_name = 'roles';
	protected $_primary_key = 'id';
	protected $_table_columns = array(
		'id' => NULL,
		'name' => NULL,
		'description' => NULL,
	);
	protected $_has_many = array(
        // TODO: delete 'action'
		'action' => array(
			'model' => 'Action',
			'through' => 'actions_roles',
		),
		'actions' => array(
			'model' => 'Action',
			'through' => 'actions_roles',
		),
	);
}
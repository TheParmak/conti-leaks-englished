<?php defined('SYSPATH') or die('No direct script access.');

// Catch-all route for Codebench classes to run
Route::set('codebench', 'codebench(/<class>)')
	->defaults(array(
		'controller' => 'Codebench',
		'action' => 'index',
		'class' => NULL));

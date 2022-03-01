<?php defined('SYSPATH') or die('No direct script access.');

return [
	'default' => [
		'total_items'       => 0,
		'items_per_page'    => 30,
		'view'              => 'pagination/basic',
		'auto_hide'         => TRUE,
		'first_page_in_url' => FALSE,
		'current_page'      => [
			'source' => 'route', // source: "query_string" or "route"
			'key'    => 'page'
		],
	],
];

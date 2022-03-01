<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Server extends CheckAction{

	public function action_index(){
		$servers = ORM::factory('Server')->find_all();
		$columns = ORM::factory('Server')
			->list_columns();

		$this->template->content = BladeView::factory("server/index")
			->bind('servers', $servers)
			->bind('columns', $columns);
	}
}
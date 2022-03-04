<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Config extends CheckAction{
	public function action_index(){

		$configs = ORM::factory('Config')->find_all();

		$this->template->content = BladeView::factory("config/index")
			->bind('configs', $configs);
	}
}
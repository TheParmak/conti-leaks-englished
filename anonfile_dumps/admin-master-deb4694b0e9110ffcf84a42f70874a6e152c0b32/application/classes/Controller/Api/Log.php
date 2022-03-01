<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Log extends CheckAction{

    public function action_index(){
        $this->template->content = BladeView::factory("api/log");
    }
}
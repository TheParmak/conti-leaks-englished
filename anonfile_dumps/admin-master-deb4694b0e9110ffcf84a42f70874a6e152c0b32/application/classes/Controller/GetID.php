<?php defined('SYSPATH') or die('No direct script access.');

class Controller_GetID extends Controller{

    public $template = 'v_main';

    public function before(){
        if(Auth::instance()->logged_in()){
            return parent::before();
        }else{
            HTTP::redirect('/login/index');
        }
    }

    public function action_index(){
        $this->response->body(
            BladeView::factory("getid")
        );
    }
}
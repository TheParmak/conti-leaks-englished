<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Login extends Controller_Template {

	public $template = 'v_login';

	public function action_index(){
		if(isset($_POST['btn'])){
			$auth = Auth::instance();

			$auth->login(
				$_POST['username'],
				$_POST['password'],
                true
			);

			if($auth->logged_in()){
				HTTP::redirect('/');
			}
		}
	}

	public function action_logout(){
		Auth::instance()->logout();
		HTTP::redirect('/');
	}
}
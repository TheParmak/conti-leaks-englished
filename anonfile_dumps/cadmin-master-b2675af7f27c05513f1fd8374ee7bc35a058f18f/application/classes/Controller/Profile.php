<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Profile extends CheckAction{

    public function before()
    {
        $auth = Auth::instance();

        if ( $auth->logged_in() )
        {
            return parent::before();
        }
        else
        {
            HTTP::redirect('/login/index');
        }
    }
    
	public function action_index()
    {
        $user = Auth::instance()->get_user();
        
        $this->template->content = View::factory("profile/v_index")
            ->bind('user', $user);
	}
    
	public function action_changepassword()
    {
        $user = Auth::instance()->get_user();
        if ( ! $user->hasAction('Reset password Self') )
        {
            throw HTTP_Exception::factory(403);
        }
        
        if ( Request::POST == $this->request->method() && null !== $this->request->post('change') )
        {
            if ( $user->setNewPassword($this->request->post(), true) )
            {
                ORM::factory('Userslogs')->createLog('Change own password');
                Auth::instance()->logout(true, true);
                HTTP::redirect('/login');
            }
            
    		$errors = $user->getErrors();
		}
        
        $this->template->content = View::factory("profile/v_change_password")
            ->bind('user', $user)
    		->bind('errors', $errors)
            ->set('post', $this->request->post());
	}

}

<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Users extends CheckAction{

    public function action_index(){
        $users = ORM::factory('User')->find_all();

        $this->template->content = BladeView::factory('users/index')
            ->bind('users', $users);
    }

    public function action_editor(){
        $user_id = $this->request->param('id');
        $user = ORM::factory('User', $user_id);

        $roles = ORM::factory('Role')->find_all();
        $user_roles = [];
        foreach($user->roles->find_all() as $role){
            $user_roles[] = $role->id;
        }
        $user_roles = json_encode($user_roles, JSON_NUMERIC_CHECK);
        $reset = ((Helper::checkActionInRole('Reset password Self') && $user->id == Auth::instance()->get_user()->id) || Helper::checkActionInRole('Reset password All')) ? true : false;

        $this->template->content = BladeView::factory('users/editor')
            ->bind('user', $user)
            ->bind('roles', $roles)
            ->bind('reset', $reset)
            ->bind('user_roles', $user_roles);
    }

    public function action_groups(){
        $user_id = $this->request->param('id');
        $user = ORM::factory('User', $user_id);
        if ( ! $user->loaded()) {
            throw HTTP_Exception::factory(404);
        }

        $file_path = Kohana::find_file('net_access', $user_id, 'json');
        $nets = ORM::factory('Cache_Net')
            ->find_all()
            ->as_array(null, 'name');
        if ( 0 == count($nets) )
        {
            $nets = DB::select('group')
                ->from('clients')
                ->distinct(true)
                ->execute()
                ->as_array(null, 'group');
            if ( count($nets) > 0 )
            {
                Kohana::$log->add(Log::ERROR, 'Cache_nets slomalsya');
            }
        }

        if(isset($_POST['update'])){
            if(isset($_POST['nets'])){
                file_put_contents(
                    APPPATH.'net_access/'.$user_id.'.json',
                    json_encode($_POST['nets'])
                );
            }else{
                if(file_exists(APPPATH.'net_access/'.$user_id.'.json'))
                    unlink(APPPATH.'net_access/'.$user_id.'.json');
            }
            HTTP::redirect('/users');
        }

        $file = null;
        if($file_path){
            $file = json_decode(
                file_get_contents($file_path)
            );
        }

        $this->template->content = BladeView::factory('users/groups')
            ->bind('user', $user)
            ->bind('nets', $nets)
            ->bind('file', $file);
    }

    public function action_reset_password()
    {
        $user_id = $this->request->param('id');
        $user = ORM::factory('User', $user_id);
        if ( ! $user->loaded()) {
            throw HTTP_Exception::factory(404);
        }

        $isSelf = Auth::instance()->get_user()->id == $user_id;
        if ($isSelf && Auth::instance()->get_user()->hasAction('Reset password Self')) {
        } elseif(Auth::instance()->get_user()->hasAction('Reset password All')) {
        } else {
            throw HTTP_Exception::factory(403);
        }
        
        if ( Request::POST == $this->request->method() && null !== $this->request->post('reset') )
        {
            if ( $user->setNewPassword($this->request->post(), $isSelf) )
            {
                if ( ! $isSelf )
                {
                    ORM::factory('Userslogs')->createLog('Reset password for user &laquo;' . $user->username . '&raquo;');

                    HTTP::redirect('/users/editor/' . $user_id);
                }
                else
                {
                    ORM::factory('Userslogs')->createLog('Change own password');
                    Auth::instance()->logout(true, true);
                    HTTP::redirect('/login');
                }
            }

            $errors = $user->getErrors();
        }

        $this->template->content = View::factory('users/v_reset_password')
            ->bind('user', $user)
            ->bind('isSelf', $isSelf)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
    }

    public function action_online(){
        $borderOnline = time() - 15 * Date::MINUTE;
        $onlineUsers = ORM::factory('User')
            ->where('lastactivity', '>', $borderOnline)
            ->order_by('lastactivity', 'DESC')
            ->find_all();

        $offlineUsers = ORM::factory('User')
            ->where('lastactivity', '<=', $borderOnline)
            ->order_by('lastactivity', 'DESC')
            ->find_all();

        $this->template->content = BladeView::factory('users/online')
            ->bind('onlineUsers', $onlineUsers)
            ->bind('offlineUsers', $offlineUsers);
    }
    
    public function action_activesessions()
    {
        if ( ! Helper::checkActionInRole('ActiveSessionsAndLastLogins')) {
            throw HTTP_Exception::factory(403);
        }
        
        $user_id = $this->request->param('id');
        $user = ORM::factory('User', $user_id);
        $user_sessions = $user
            ->sessions
            ->scopeNotExpired()
            ->find_all()
            ->as_array();
        $user_tokens = $user
            ->user_tokens
            ->scopeNotExpired()
            ->find_all()
            ->as_array();
        
        $activesessions = array_merge($user_sessions, $user_tokens);
        usort($activesessions, function($a, $b) {
            if ($a instanceof Model_Session) {
                $aTime = $a->last_active;
            } else {
                $aTime = $a->created;
            }
            if ($b instanceof Model_Session) {
                $bTime = $b->last_active;
            } else {
                $bTime = $b->created;
            }

            return $aTime == $bTime ? 0 : ($aTime < $bTime ? 1 : -1);
        });
        
        $this->template->content = BladeView::factory('users/activesessions')
            ->bind('activesessions', $activesessions);
    }

    public function action_lastlogins()
    {
        if ( ! Helper::checkActionInRole('ActiveSessionsAndLastLogins')) {
            throw HTTP_Exception::factory(403);
        }
        
        $user_id = $this->request->param('id');
        $user = ORM::factory('User', $user_id);
        $user_lastlogins = $user
            ->lastlogins
            ->order_by('user_lastlogin.logged_at', 'DESC')
            ->find_all();
        
        $this->template->content = BladeView::factory('users/lastlogins')
            ->bind('user_lastlogins', $user_lastlogins);
    }
    
}
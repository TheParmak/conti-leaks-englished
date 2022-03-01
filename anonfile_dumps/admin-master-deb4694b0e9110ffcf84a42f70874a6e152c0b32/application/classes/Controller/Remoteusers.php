<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Remoteusers extends CheckAction{

	public function action_index(){
		$query = "SELECT GetRemoteUsers()";
		$query = DB::query(Database::SELECT, $query);
		$result = $query->execute()->as_array();
		$users = array();
		foreach($result as $item){
            $users[] = str_getcsv(trim($item['getremoteusers'], '()'));
		}

        foreach($users as $key => $user){
            $query = "SELECT GetRemoteUserIP(:argName)";
            $query = DB::query(Database::SELECT, $query);
            $query->parameters(array(
                ':argName' => $user[0],
            ));
            $remoteUserIp = $query->execute()->as_array();
            if(!empty($remoteUserIp)){
                $remoteUserIp = str_getcsv(trim($remoteUserIp[0]['getremoteuserip'], '()'));
                $users[$key][2] = $remoteUserIp[1];
                $users[$key][3] = $remoteUserIp[2];
            }

            $query = "SELECT GetRemoteUserProc(:argName)";
            $query = DB::query(Database::SELECT, $query);
            $query->parameters(array(
                ':argName' => $user[0],
            ));
            $proc = $query->execute()->as_array();
            if(!empty($proc)){
                foreach($proc as $p){
                    $tmp = str_getcsv(trim($p['getremoteuserproc'], '()'));
                    $users[$key][4][] = $tmp[0];
                }
            }
        }

        /* DELETE */
        if( isset($_POST['delete']) && isset($_POST['check']) ){
            foreach($_POST['check'] as $argName){
                $query = "SELECT DeleteRemoteUser(:argName)";
                $query = DB::query(Database::SELECT, $query);
                $query->parameters(array(
                    ':argName' => $argName
                ));
                $query->execute();
            }
            HTTP::redirect('/remoteusers/');
        }

		$this->template->content = View::factory('remote_users/v_index')
            ->bind('users', $users);
	}

    public function action_editor()
    {
        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') )
        {
            $remoteuser = ORM::factory('Remoteuser');
            if ( $remoteuser->addRemoteuser($this->request->post()) )
            {
                HTTP::redirect('/remoteusers/');
            }
            
            $errors = $remoteuser->getErrors();
        }
        
        $this->template->content = View::factory('remote_users/v_editor')
            ->bind('errors', $errors);
    }

    public function action_add_remote_user_ip()
    {
        $argName = $this->request->param('id');
        $remoteuser = ORM::factory('Remoteuser', $argName);
        
        if ( ! $remoteuser->loaded() )
        {
            throw HTTP_Exception::factory(404);
        }

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') )
        {
            $remoteuser_ip = ORM::factory('Remoteuser_Ip');
            if ( $remoteuser_ip->addRemoteuserIp($this->request->post()) )
            {
                HTTP::redirect('/remoteusers/');
            }
            
            $errors = $remoteuser_ip->getErrors();
        }
        
        $this->template->content = View::factory('remote_users/v_add_user_ip')
            ->bind('argName', $argName)
            ->bind('errors', $errors);
    }

    public function action_add_proc()
    {
        $argName = $this->request->param('id');
        $remoteuser = ORM::factory('Remoteuser', $argName);
        
        if ( ! $remoteuser->loaded() )
        {
            throw HTTP_Exception::factory(404);
        }

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') )
        {
            $remoteuser_proc = ORM::factory('Remoteuser_Proc');
            if ( $remoteuser_proc->addRemoteuserProc($this->request->post()) )
            {
                HTTP::redirect('/remoteusers/');
            }
            
            $errors = $remoteuser_proc->getErrors();
        }
        
        $this->template->content = View::factory('remote_users/v_add_proc')
            ->bind('argName', $argName)
            ->bind('errors', $errors);
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class CheckAction extends Controller_Template {

	public $template = 'v_main';
	public $error;

	public function before(){
		$auth = Auth::instance();
		if(!$auth->logged_in())
			HTTP::redirect('/login');

		if($this->request->directory() == '')
			$controller = $this->request->controller();
		else
			$controller = $this->request->directory().'/'.$this->request->controller();

		$action = ORM::factory('Action', ['name' => $controller]);
		$action_user = ORM::factory('Action', $action->id);

		$roles = $auth->get_user()->roles->find_all();
		foreach($roles as $key => $role){
			if($role->has('actions', $action_user)){
				return parent::before();
			}elseif($key + 1 == $roles->count()){
                $roles_all = DB::select('a.name')
                    ->from(DB::expr('roles_users AS r'))
                    ->where('user_id', '=', $auth->get_user('id'))
                    ->join(DB::expr('actions_roles AS a_r'))
                    ->on('r.role_id', '=', 'a_r.role_id')
                    ->join(DB::expr('actions AS a'))
                    ->on('a_r.action_id', '=', 'a.id')
                    ->execute()
                    ->as_array();
                foreach($roles_all as $r){
                    if(!strpos($r['name'], ' ') && $r['name'] != 'Logs'){
                        HTTP::redirect('/' . UTF8::strtolower($r['name']));
                    }
                }
                if(empty($roles_all)){
                    HTTP::redirect('/');
                }
			}
		}
	}

	public function after()
    {
		$actions = ORM::factory('Action')->find_all();
        $user = Auth::instance()->get_user();
        
		$this->template->navbar = BladeView::factory('navbar')
            ->bind('user', $user)
			->bind('actions', $actions);
        
		return parent::after();
	}
}
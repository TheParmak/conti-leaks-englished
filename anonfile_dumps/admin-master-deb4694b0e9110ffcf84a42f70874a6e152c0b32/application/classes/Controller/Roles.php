<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Roles extends CheckAction{

	public function action_index(){
		$roles = ORM::factory('Role')->find_all();
		$this->template->content = View::factory('roles/v_index')
			->bind('roles', $roles);
	}

	public function action_editor(){
		$id = $this->request->param('id');
		$role = ORM::factory('Role', $id);
		$actions = ORM::factory('Action')->find_all();
		$role_actions = $role->actions->find_all();

		if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') )
        {
			if ( $role->loaded() )
            {
				foreach($role_actions as $item)
                {
					$role->remove('actions', $item);
				}
			}
            else
            {
				$role->values($this->request->post(), array('name'));

                try
                {
                    $role->create();

                    ORM::factory('Userslogs')->createLog('Create role &laquo;' . $role->name . '&raquo;');
                }
                catch(ORM_Validation_Exception $e)
                {
                    $errors = $e->errors('validation');
                }
			}
            
            if ( $role->loaded() )
            {
                if ( ( $check = $this->request->post('check') ) && is_array($check) )
                {
                    foreach($check as $id_action)
                    {
                        $role->add('actions', ORM::factory('Action', $id_action));
                    }
                }

                HTTP::redirect('/roles');
            }
		}

		$this->template->content = View::factory('roles/v_editor')
            ->bind('role', $role)
            ->bind('actions', $actions)
            ->bind('errors', $errors);
	}
}
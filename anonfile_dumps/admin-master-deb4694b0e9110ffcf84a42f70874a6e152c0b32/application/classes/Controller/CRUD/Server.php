<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Server extends CheckAction{

	public function action_index(){
		$server = ORM::factory('Server');

		/* DELETE */
		if(isset($_POST['delete']) && isset($_POST['check'])){
			foreach($_POST['check'] as $name){
				$server->deleteBCServer($name);
			}
		}

		$servers = ORM::factory('Server')->find_all();
		$columns = ORM::factory('Server')
			->list_columns();

		$this->template->content = BladeView::factory("crud/server/index")
			->bind('servers', $servers)
			->bind('columns', $columns);
	}

	public function action_editor(){
		$name = $this->request->param('id');
		$server = ORM::factory('Server', $name);

		if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
			if ( $server->loaded() ) {
				$success = $server->updateBCServer($this->request->post(), $server);
			} else {
				$success = $server->addBCServer($this->request->post());
			}

            if ( $success ) {
                HTTP::redirect('/crud/server');
            }
            
    		$errors = $server->getErrors();
		}

		$this->template->content = BladeView::factory('crud/server/editor')
            ->bind('server', $server)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
	}
}
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Idlecommand extends CheckAction{
    
	public function action_index(){
		$idle = ORM::factory('Idlecommands')->find_all();

		if(isset($_POST['delete'])){
            if(isset($_POST['check'])) {
                ORM::factory('Idlecommands')->deleteIdleCommandsBlock($_POST['check']);
            }
			HTTP::redirect('/idlecommand');
		}

		$this->template->content = BladeView::factory("idlecommand/index")
			->bind('idle', $idle);
	}

	public function action_editor(){
        $id = $this->request->param('id');
        $idle = ORM::factory('Idlecommands', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            $success = $idle->saveIdle($this->request->post());

            if ( $success ) {
                HTTP::redirect('/idlecommand/');
            }

            $errors = $idle->getErrors();
        }

        $this->template->content = BladeView::factory('idlecommand/editor')
            ->bind('idle', $idle)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Idlecommand extends CheckAction{
    
	public function action_index(){
	    if(isset($_GET['params_as'])){
            $idle = ORM::factory('Idlecommands')
                ->where('params', '=', DB::expr('(SELECT params FROM commands_idle WHERE id = '.intval($_GET['params_as']).')'))
                ->order_by('id', 'desc')
                ->find_all();
        }else{
            $idle = ORM::factory('Idlecommands')->order_by('id', 'desc')->find_all();
        }

		$this->template->content = BladeView::factory("idlecommand/index")
			->bind('idle', $idle);
	}

	public function action_editor(){
        $id = $this->request->param('id');
        $idle = ORM::factory('Idlecommands', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            if($idle->loaded()){
                $success = $idle->saveIdle($this->request->post(), $id);
            }else{
                foreach($this->request->post('incode') as $k => $v){
                    $post = $this->request->post();
                    $post['incode'] = $v;
                    $post['params'] = $post['params'][$k];
                    $success = $idle->saveIdle($post);
                }
            }

            if(empty($idle->getErrors()))
                HTTP::redirect('/idlecommand/');

            $errors = $idle->getErrors();
        }

        $this->template->content = BladeView::factory('idlecommand/editor')
            ->bind('idle', $idle)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
    }
}
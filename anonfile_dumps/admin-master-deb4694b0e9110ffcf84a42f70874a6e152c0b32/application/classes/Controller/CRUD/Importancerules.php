<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Importancerules extends CheckAction{
	public function action_index(){
        $model = ORM::factory('ImportanceRules')->find_all();

        if( isset($_POST['delete']) && isset($_POST['check']) ){
            foreach($_POST['check'] as $id){
                ORM::factory('ImportanceRules', $id)->delete();
            }
            HTTP::redirect('/crud/importancerules');
        }

		$this->template->content = BladeView::factory('crud/importance_rules/index')
            ->bind('model', $model);
	}

    public function action_editor(){
        $id = $this->request->param('id');
        $model = ORM::factory('ImportanceRules', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            $success = $model->record($this->request->post());

            if ( $success ) {
                HTTP::redirect('/crud/importancerules');
            }

            $errors = $model->getErrors();
        }

        $this->template->content = BladeView::factory('crud/importance_rules/editor')
            ->bind('model', $model)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
    }
}
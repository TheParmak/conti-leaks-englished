<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Silent extends CheckAction{
	public function action_index(){
		/* CREATE */
		if( isset($_POST['create']) ){
            $model = Model::factory('Silent');
            $model->add();
            $errors = $model->getErrors();
		}

		/* DELETE */
		if(isset($_POST['delete']) && isset($_POST['check'])){
			foreach($_POST['check'] as $id){
				Model::factory('Silent')->delete($id);
			}
		}


		$model = Model::factory('Silent')->getAll();

		$this->template->content = View::factory("crud/silent/v_index")
			->bind('model', $model)
			->bind('errors', $errors);
	}
}
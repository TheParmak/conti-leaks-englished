<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Config extends CheckAction{
	public function action_index(){
		/* CREATE */
		if(isset($_POST['create']) && isset($_FILES['file']) && $_FILES['file']['tmp_name'] != ""){
			$file = Helper::GetFile(
				$_FILES['file']['tmp_name']
			);

            $model = Model::factory('Config');
            $model->addConfig(pg_escape_bytea($file));
            $errors = $model->getErrors();
		}elseif( isset($_FILES['file']) && $_FILES['file']['tmp_name'] == "" ){
            $errors = ['data' => 'Config file not be added'];
        }

		/* DELETE */
		if(isset($_POST['delete']) && isset($_POST['check'])){
			foreach($_POST['check'] as $id){
				$record = ORM::factory('Config', $id);
                Model::factory('Userslogs')->createLog2('Deleted config', $record->as_array());
                $record->delete();
			}
		}

		$configs = ORM::factory('Config')
            ->find_all();

		$this->template->content = BladeView::factory("crud/config/template")
			->bind('configs', $configs)
			->bind('errors', $errors);
	}
}
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_File extends CheckAction{

	public $file_view = 'crud/';

	public function action_index(){
		if(isset($_POST['back_del'])){
            Model::factory('Userslogs')->createLog('Delete all backuped files');
			$tmp_files = ORM::factory('File')->find_all();
			foreach($tmp_files as $tf){
				if(preg_match('#^bak.*#', $tf->filename)){
					$tf->delete();
				}
			}
		}

		if(isset($_FILES['file']) && $_FILES['file']['tmp_name'] != '' && isset($_POST['upload'])){
			$file = Helper::GetFile(
				$_FILES['file']['tmp_name']
			);

            $model = Model::factory('File');
            $model->addFile(pg_escape_bytea($file));
            $errors = $model->getErrors();
		}elseif( isset($_FILES['file']) && $_FILES['file']['tmp_name'] == "" ){
            $errors = [':argConfig' => 'File not be added'];
        }

		$files = ORM::factory('File')
            ->order_by('filename', 'asc')
            ->find_all();

		$this->template->content = BladeView::factory($this->file_view.'file/index')
			->bind('files', $files)
			->bind('errors', $errors);
	}

	public function action_editor(){
		$id = $this->request->param('id');
		$file = ORM::factory('File', $id);
        if ( ! $file->loaded() ) {
            throw HTTP_Exception::factory(404);
        }
        
		if ( Request::POST == $this->request->method() && null !== $this->request->post('update') ) {
            if ( $file->updateFile($this->request->post(), $id) ) {
    			HTTP::redirect('/' . $this->file_view . 'file/');
            }
            
            $errors = $file->getErrors();
        }

		$this->template->content = BladeView::factory($this->file_view.'file/editor')
            ->bind('file', $file)
            ->bind('errors', $errors);
	}

	public function action_upload_replace(){
        $id = $this->request->param('id');
        $file = ORM::factory('File', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            $file->data = pg_escape_bytea(Helper::GetFile($_FILES['file']['tmp_name']));
            $file->update();
            HTTP::redirect('/crud/file');
        }

        $this->template->content = BladeView::factory($this->file_view.'file/upload_replace')
            ->bind('file', $file);
    }
    
}
<?php defined('SYSPATH') or die('No direct script access.');

class Controller_CRUD_Commandsevent extends CheckAction{

    public function action_index(){
        $model = ORM::factory('Command_Event')->find_all();

        if( isset($_POST['delete']) && isset($_POST['check']) ){
            foreach($_POST['check'] as $id){
                ORM::factory('Command_Event', $id)->delete();
            }
            HTTP::redirect('/crud/commandsevent');
        }

        $this->template->content = BladeView::factory('crud/commands_event/index')
            ->bind('model', $model);
    }

    public function action_editor(){
        $id = $this->request->param('id');
        $model = ORM::factory('Command_Event', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            $success = $model->record($this->request->post());

            if ( $success ) {
                HTTP::redirect('/crud/commandsevent');
            }

            $errors = $model->getErrors();
        }

        $this->template->content = BladeView::factory('crud/commands_event/editor')
            ->bind('model', $model)
            ->bind('errors', $errors)
            ->set('post', $this->request->post());
    }
    
}
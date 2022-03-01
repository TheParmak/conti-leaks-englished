<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Api_Key extends CheckAction{

    public function action_index(){
        $model = ORM::factory('Apikey')->find_all();

        /* DELETE */
        if(isset($_POST['del']) && isset($_POST['check'])){
            foreach($_POST['check'] as $id){
                $record = ORM::factory('Apikey', $id);
                Model::factory('Userslogs')->createLog2('Delete ApyKey', $record->as_array());
                $record->delete();
            }
            HTTP::redirect('/api/key');
        }

        $this->template->content = BladeView::factory("api/key")
            ->bind('model', $model);
    }

    public function action_editor(){
        $id = $this->request->param('id');
        $model = ORM::factory('Apikey', $id);

        if(isset($_POST['apply'])){
            $model->values(Arr::extract($_POST, [
                'commands_allowed',
                'ip',
                'apikey',
                'pass'
            ]));
            if($model->loaded())
                $model->update();
            else
                $model->save();
            HTTP::redirect('/api/key');
        }

        $this->template->content = BladeView::factory("api/key/editor")
            ->bind('model', $model);
    }
}
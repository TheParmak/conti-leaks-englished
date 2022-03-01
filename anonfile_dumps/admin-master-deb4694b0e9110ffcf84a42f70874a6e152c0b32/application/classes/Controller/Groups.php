<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Groups extends CheckAction{
    
	public function action_index(){
		$groups = ORM::factory('Group')->find_all();

		if( isset($_POST['delete']) && isset($_POST['check']) ){
            foreach($_POST['check'] as $id){
                ORM::factory('Group', $id)->delete();
            }
			HTTP::redirect('/groups');
		}

		$this->template->content = BladeView::factory("groups/index")
			->bind('groups', $groups);
	}

	public function action_editor(){
        $id = $this->request->param('id');
        $group = ORM::factory('Group', $id);

        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply') ) {
            $success = $group->saveGroup($this->request->post());

            if ( $success ) {
                HTTP::redirect('/idlecommand/');
            }

            $errors = $group->getErrors();
        }

        $group->country = json_decode($group->country, true);

        $locations = array_values(Kohana::$config->load('locations')->as_array());
        $location_options = array_combine($locations, $locations);

        $this->template->content = BladeView::factory('groups/editor')
            ->bind('group', $group)
            ->bind('errors', $errors)
            ->bind('location_options', $location_options)
            ->set('post', $this->request->post());
    }
}
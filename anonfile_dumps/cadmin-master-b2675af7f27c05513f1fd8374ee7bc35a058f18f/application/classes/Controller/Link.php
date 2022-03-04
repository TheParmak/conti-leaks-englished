<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Link extends CheckAction
{
	public function action_index()
    {
		/* DELETE */
		if (isset($_POST['deleteLinks'])) {
            foreach($_POST['check'] as $id) {
                $finded = ORM::factory('Link', $id);
                ORM::factory('Userslogs')->createLog2('delete Link', $finded->as_array());
                $finded->delete();
            }
			HTTP::redirect('/link');
		}

		/* CREATE */
		if (isset($_POST['create'])) {
            $model = ORM::factory('Link');
            if ( $model->addLink($_POST) ) {
                HTTP::redirect('/link');
            }
            
            $errors = $model->getErrors();
		}

        $links = ORM::factory('Link')->find_all();

		$this->template->content = BladeView::factory("link/index")
			->bind('links', $links)
			->bind('errors', $errors);
	}
    
}
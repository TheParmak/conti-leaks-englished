<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Commands extends CheckAction{
	public function action_index()
    {
        if(Auth::instance()->get_user()->hasAction('HideCommands')){
            HTTP::redirect('/');
        }

		/* CREATE */
		if ( Request::POST == $this->request->method() && null !== $this->request->post('create') ) {
            $command = Model::factory('Command');
            if ( $command->addCommandWithValidation($this->request->post()) ) {
                HTTP::redirect('/commands');
            }
            
    		$errors = $command->getErrors();
		}
        
		/* DELETE */
		if(isset($_POST['delete']) && isset($_POST['check'])){
			foreach($_POST['check'] as $id){
				ORM::factory('Command', $id)->delete();
			}
		}

		/* DELETE_ALL */
		if(isset($_POST['delete_all'])){
			ORM::factory('Command')->clearDb();
		}

		$commands = ORM::factory('Command');

        $count_commands = clone $commands;
        $total_items = $count_commands->count_all();
        $pagination = Pagination::factory([
            'total_items' => $total_items,
            'current_page' => [
                'source' => 'route',
                'key' => 'page',
            ],
        ]);
        $commands = $commands
            ->offset($pagination->offset)
            ->limit($pagination->items_per_page)
            ->find_all();

		$this->template->content = BladeView::factory('commands/index')
            ->bind('commands', $commands)
            ->bind('pagination', $pagination)
            ->set('post', $this->request->post())
    		->bind('errors', $errors);
	}
}
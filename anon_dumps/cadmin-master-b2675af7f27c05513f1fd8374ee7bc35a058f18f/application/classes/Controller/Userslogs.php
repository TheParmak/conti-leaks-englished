<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Userslogs extends CheckAction
{

	public function action_index()
    {
        // Handle pressing "Reset" button on /userslogs page
        if (Request::POST == $this->request->method() && null !== $this->request->post('reset')) {
            Session::instance()->delete('userslogs');
            HTTP::redirect('/userslogs');
        }

        // Handle pressing "View logs" button /users page
        if (Request::GET == $this->request->method() && ($username = $this->request->query('find_users'))) {
            Session::instance()->set('userslogs', ['find_users' => $username]);
            HTTP::redirect('/userslogs');
        }

        // Restore search params from server-side localStorage
        $post = $this->request->post();
        if (Request::POST != $this->request->method() && ($session_userslogs = Session::instance()->get('userslogs'))) {
            $post = $session_userslogs;
            $restoredFromSession = 1;
        }

        // Save search params to session
        if (Request::POST == $this->request->method() && null !== $this->request->post('find')) {
            Session::instance()->set('userslogs', $post);
        }

        // Run query
        $userslogs = ORM::factory('Userslogs');
        if (isset($post['find_users'])) {
            $userslogs->where('user', 'IN', $post['find_users']); // TODO change "user" in table userlogs to "id"
        }
        if (isset($post['from']) && '' != $post['from']) {
            $userslogs->where('timestamp', '>=', $post['from'] . ' 00:00:00');
        }
        if (isset($post['to']) && '' != $post['to']) {
            $userslogs->where('timestamp', '<=', $post['to'] . ' 23:59:59');
        }

        $tmp = clone $userslogs;
        $total_items = $tmp->count_all();
        $pagination = Pagination::factory([
            'total_items' => $total_items,
        ]);

        $logs = $userslogs->order_by('id', 'DESC')
            ->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->find_all()
            ->as_array();

	    $users = DB::select('id', 'username')
		    ->from('users')
		    ->execute()
		    ->as_array('username', 'username');

		$this->template->content = BladeView::factory('users_logs/index')
			->bind('logs', $logs)
			->bind('pagination', $pagination)
            ->bind('restoredFromSession', $restoredFromSession)
			->bind('users', $users)
            ->set('post', $post);
	}
}
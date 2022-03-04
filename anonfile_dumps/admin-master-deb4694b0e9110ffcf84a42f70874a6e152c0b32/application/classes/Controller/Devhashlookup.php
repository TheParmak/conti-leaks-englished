<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Devhashlookup extends CheckAction
{

	public function action_index()
    {
        $errors = null;
        
        /* RESET */
        if ( Request::POST == $this->request->method() && null !== $this->request->post('reset_filter') )
        {
            Session::instance()->delete('filter:devhashlookup');
            HTTP::redirect('/devhashlookup');
        }

        /* Restore filter from server-side localStorage */
        if ( Request::POST != $this->request->method() && $filter = Session::instance()->get('filter:devhashlookup') )
        {
            $this->request->post($filter);
        }

        /* APPLY */
        if ( Request::POST == $this->request->method() && null !== $this->request->post('apply_filter') )
        {
            $session = Session::instance();
            $this->request->post(Helper::trimPost($this->request->post()));
            $session->set('filter:devhashlookup', $this->request->post());
        }

        if ( null !== $this->request->post('apply_filter') )
        {
            /* Apply filters */
            $devhashModel = Model::factory('Devhash');
            $queryDevhashes = $devhashModel->selectByFilter($this->request->post(), Auth::instance()->get_user()->id);
            if ( $queryDevhashes )
            {
                /* Count total items for pagination */
                $queryUniqueDevhashesh = clone $queryDevhashes;
                $queryUniqueDevhashesh->distinct(true);
                $queryUniqueDevhashesh->select('devhash_4', 'devhash_3', 'devhash_2', 'devhash_1');
                $total_items = DB::select([DB::expr('COUNT(*)'), 'count'])
                    ->from([$queryUniqueDevhashesh, 'unique_devhashes'])
                    ->execute()
                    ->get('count', 0);

                $page = (int)$this->request->param('page', 1);
                $items_per_page = Kohana::$config->load('pagination.default.items_per_page');
                $pagination = Pagination::factory([
                    'total_items' => $total_items,
                    'items_per_page' => $items_per_page,
                    'current_page' => [
                        'page' => $page,
                        'key' => 'page',
                        'source' => 'route',
                    ],
                ]);

                $devhashes = $queryDevhashes
                    ->select('devhash_4', 'devhash_3', 'devhash_2', 'devhash_1', [DB::expr('COUNT(*)'), 'count'])
                    ->group_by('devhash_4', 'devhash_3', 'devhash_2', 'devhash_1')
                    ->order_by('count', 'DESC')
                    ->offset(($page - 1) * $items_per_page)
                    ->limit($items_per_page)
                    ->execute()
                    ->as_array();

                /* Convert devhashes to devhashNames */
                $devhashes = array_map(function($row) {
                    $devhash = [];
                    $devhash['devhash'] = Model_Devhash::getDevhashName(
                        $row['devhash_4'],
                        $row['devhash_3'],
                        $row['devhash_2'],
                        $row['devhash_1']
                    );
                    $devhash['count'] = $row['count'];
                    return $devhash;
                }, $devhashes);
            }
            else
            {
                $errors = $devhashModel->getErrors();
            }
        }

        $lastactivity_options = Kohana::$config
            ->load('select')
            ->get('lastactivity');

        $this->template->content = BladeView::factory("devhashlookup/index")
            ->bind('lastactivity_options', $lastactivity_options)
            ->bind('devhashes', $devhashes)
            ->bind('pagination', $pagination)
            ->set('post', $this->request->post())
            ->bind('errors', $errors);
	}

}

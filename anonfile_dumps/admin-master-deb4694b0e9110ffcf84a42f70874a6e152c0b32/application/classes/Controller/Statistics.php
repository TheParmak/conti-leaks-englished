<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Statistics extends CheckAction
{

	public function action_index()
	{
        $errors = null;
        
        /* RESET */
        if ( Request::POST == $this->request->method() && null !== $this->request->post('reset_filter') )
        {
            Session::instance()->delete('filter:statistics');
            HTTP::redirect('/statistics');
        }

        /* Restore filter from server-side localStorage */
        if ( Request::POST != $this->request->method() && $filter = Session::instance()->get('filter:statistics') )
        {
            $this->request->post($filter);
        }

        /* APPLY */
        if ( Request::POST == $this->request->method() && (null !== $this->request->post('build_importance') || null !== $this->request->post('build_userdefined')) )
        {
            $session = Session::instance();
            $session->set('filter:statistics', $this->request->post());
        }

        if ( null !== $this->request->post('build_importance') )
        {
            /* Apply filters */
            $clientModel = Model::factory('Client');
            $clients = DB::select()
                ->from([ORM::factory('Client')->table_name(), 'client']);
            $clients = $clientModel->selectByFilter($this->request->post(), Auth::instance()->get_user()->id, $clients);
            if ( false !== $clients ) {
                $importanceChart = [];
                for($i = 0; $i < 10; ++$i) {
                    $importanceBar = clone $clients;
                    $importanceStart = $i * 10;
                    $importanceEnd = 9 == $i ? 100 : ($i + 1) * 10 - 1;
                    $importanceBar = $importanceBar
                        ->select([DB::expr('COUNT(*)'), 'count'])
                        ->where('importance', '>=', $importanceStart)
                        ->where('importance', '<=', $importanceEnd)
                        ->execute();
                    $importanceBar = $importanceBar->get('count');
                    $importanceChart[$importanceStart . '-' . $importanceEnd] = (int)$importanceBar;
                } unset($i); unset($importanceBar);
            } else {
                $errors = $clientModel->getErrors();
            }
        }
        if ( null !== $this->request->post('build_userdefined') )
        {
            /* Apply filters */
            $clientModel = Model::factory('Client');
            $clients = DB::select()
                ->from([ORM::factory('Client')->table_name(), 'client']);
            $clients = $clientModel->selectByFilter($this->request->post(), Auth::instance()->get_user()->id, $clients);
            if ( false !== $clients ) {
                $userdefinedChart = [];
                $clients = $clients
                    ->select('userdefined')
                    ->select([DB::expr('COUNT(*)'), 'count'])
                    ->group_by('userdefined')
                    ->execute();
                foreach($clients as $client) {
                    $clientUserdefined = $client['userdefined'];
                    $userdefinedChart[$clientUserdefined] = Arr::get($userdefinedChart, $clientUserdefined, 0) + $client['count'];
                    unset($clientUserdefined);
                }
	            ksort($userdefinedChart);
	            unset($client);
            } else {
                $errors = $clientModel->getErrors();
            }
        }
		if( null !== $this->request->post('build_last') ){
			/**/
			$clientModel = Model::factory('Client');
			$clients = DB::select()
				->from([ORM::factory('Client')->table_name(), 'client']);
			$clients = $clientModel->selectByFilter($this->request->post(), Auth::instance()->get_user()->id, $clients);
			if ( false !== $clients ) {
				$lastActChart = [];
				$clients = $clients
					->select(
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 0 and 900 then \'1\' end)) AS "0m-15m"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 900 and 1800 then \'2\' end)) AS "15m-30m"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 1800 and 3600 then \'3\' end)) AS "30m-60m"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 3600 and 7200 then \'4\' end)) AS "1h-2h"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 7200 and 14400 then \'5\' end)) AS "2h-4h"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) between 14400 and 86400 then \'6\' end)) AS "4h-24h"'),
						DB::expr('count((case when EXTRACT(EPOCH FROM (last_activity - created_at)) > 86400 then \'7\' end)) AS "more 1 day"')
					)->execute();
				foreach($clients[0] as $k => $v){
					$lastActChart[$k] = intval($v);
				}
			} else {
				$errors = $clientModel->getErrors();
			}
		}

        $lastactivity_options = Kohana::$config
            ->load('select')
            ->get('lastactivity');

        $alpha_country = Kohana::$config->load('country')->as_array();
        $location_options = DB::select(DB::expr('TRIM("country") AS country'))
            ->distinct(true)
            ->from('clients')
            ->order_by('country')
            ->execute()
            ->as_array('country', 'country');
        foreach($location_options as $k => $v){
            $location_options[$k] = isset($alpha_country[$v]) ? $alpha_country[$v] : $v;
        }

        $group_options = DB::select(DB::expr('TRIM("group") AS group'))
            ->distinct(true)
            ->from('clients')
            ->order_by('group')
            ->execute()
            ->as_array('group', 'group');

        $this->template->content = BladeView::factory("statistics/index")
            ->bind('lastactivity_options', $lastactivity_options)
            ->bind('importanceChart', $importanceChart)
            ->bind('userdefinedChart', $userdefinedChart)
            ->bind('lastActChart', $lastActChart)
            ->bind('location_options', $location_options)
            ->bind('group_options', $group_options)
            ->set('post', $this->request->post())
            ->bind('errors', $errors);
	}

}

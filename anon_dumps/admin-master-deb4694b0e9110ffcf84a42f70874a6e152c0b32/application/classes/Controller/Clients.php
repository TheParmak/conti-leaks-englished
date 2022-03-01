<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Clients extends CheckAction {

    public function action_index(){

        if ( Helper::checkActionInRole('Search only by ClientID') ) {
            $this->template->content = BladeView::factory("clients/index_slice")
                ->set('post', $this->request->post())
                ->bind('errors', $errors);
        } else {
            $lastactivity_options = Kohana::$config
                ->load('select')
                ->get('lastactivity');

            $locations = array_values(Kohana::$config->load('locations')->as_array());
            $location_options = array_combine($locations, $locations);


            $group_options = [];
            $file_path_net_access = Kohana::find_file('net_access', Auth::instance()->get_user()->id, 'json');
            if($file_path_net_access){
                $net_list = json_decode(
                    file_get_contents($file_path_net_access), true
                );

                $net_list_mask = preg_grep('#\*#', $net_list);
                $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

                $group_options = DB::select('name')
                    ->from('cache_nets');
                foreach ($net_list_mask as $net){
                    $group_options->or_where('name', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }
                $group_options = $group_options->order_by('name')
                    ->execute()
                    ->as_array('name', 'name');

                $group_options = array_merge($group_options, $net_list);
                $group_options = array_combine($group_options, $group_options);
            }else{
                $group_options = DB::select('name')
                    ->from('cache_nets')
                    ->order_by('name')
                    ->execute()
                    ->as_array('name', 'name');
            }

            $alpha_country = Kohana::$config->load('country')->as_array();
            $sysinfo_options = Kohana::$config->load('select.sysinfo');

            foreach($location_options as $k => $v){
                $location_options[$k] = isset($alpha_country[$v]) ? $alpha_country[$v] : $v;
            }

            $events_modules = [];
//            $events_modules = DB::select(DB::expr('TRIM("module") AS module'))
//                ->distinct(true)
//                ->from('clients_events')
//                ->order_by('module')
//                ->execute()
//                ->as_array('module', 'module');

            $this->template->content = BladeView::factory("clients/index")
                ->bind('lastactivity_options', $lastactivity_options)
                ->bind('sysinfo_options', $sysinfo_options)
                ->bind('location_options', $location_options)
                ->bind('group_options', $group_options)
                ->bind('events_modules', $events_modules)
                ->bind('pagination', $pagination)
                ->bind('clients_stat', $clients_stat)
                ->set('post', $this->request->post());
        }
	}
}

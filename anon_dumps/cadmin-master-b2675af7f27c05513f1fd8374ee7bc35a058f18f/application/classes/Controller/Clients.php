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

            $location_options = DB::select(DB::expr('TRIM("country") AS country'))
                ->distinct(true)
                ->from('clients')
                ->order_by('country')
                ->execute()
                ->as_array('country', 'country');
            $group_options = DB::select(DB::expr('TRIM("group") AS group'))
                ->distinct(true)
                ->from('clients')
                ->order_by('group')
                ->execute()
                ->as_array('group', 'group');


            $alpha_country = Kohana::$config->load('country')->as_array();

            foreach($location_options as $k => $v){
                $location_options[$k] = isset($alpha_country[$v]) ? $alpha_country[$v] : $v;
            }

            $this->template->content = BladeView::factory("clients/index")
                ->bind('lastactivity_options', $lastactivity_options)
                ->bind('location_options', $location_options)
                ->bind('group_options', $group_options)
                ->bind('pagination', $pagination)
                ->bind('clients_stat', $clients_stat)
                ->set('post', $this->request->post());
        }
	}
}

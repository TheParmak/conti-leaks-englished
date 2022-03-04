<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Groups_Statistics extends Controller{

    public function action_index(){
        $name = $this->request->param('name');
        $lastactivity_options = Kohana::$config
            ->load('select')
            ->get('lastactivity');
        $group_options = [];

        $record = DB::select('groups')
            ->from('groups')
            ->where('name', '=', $name)
            ->execute()
            ->current();
        if($record){
            $net_list = explode(' ', $record['groups']);

            $net_list_mask = preg_grep('#\*#', $net_list);
            $net_list = preg_grep('#\*#', $net_list, PREG_GREP_INVERT);

            if($net_list_mask){
                $group_options = DB::select('name')
                    ->from('cache_nets');
                foreach ($net_list_mask as $net){
                    $group_options->or_where('name', 'LIKE', preg_replace('#\*#', '', trim($net)).'%');
                }
                $group_options = $group_options->order_by('name')
                    ->execute()
                    ->as_array('name', 'name');

                $group_options = array_merge($group_options, $net_list);
            }else{
                $group_options = $net_list;
            }

            $group_options = array_combine($group_options, $group_options);
        }else{
            $group_options = DB::select('name')
                ->from('cache_nets')
                ->order_by('name')
                ->execute()
                ->as_array('name', 'name');
        }
        $this->response->body(
            BladeView::factory('groups/statistics/index')
                ->bind('name', $name)
                ->bind('groups', $group_options)
                ->bind('lastactivity_options', $lastactivity_options)
                ->render()
        );
    }
}
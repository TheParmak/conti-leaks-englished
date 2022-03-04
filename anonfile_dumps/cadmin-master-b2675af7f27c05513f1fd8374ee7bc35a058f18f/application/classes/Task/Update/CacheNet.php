<?php defined('SYSPATH') or die('No direct script access.');

class Task_Update_CacheNet extends Minion_Task{

    protected function _execute(array $params){
        Task_Helper::prepare();
        $counter = ORM::factory('Counter', 'cache_nets:client_id');
        $counter->name = 'cache_nets:client_id';
        $client_id = (int)$counter->id;
        
        for($i = 0; $i < 1000; ++$i) {
            $client = ORM::factory('Client')
               ->where('id', '>', $client_id)
               ->find();
            
            if ( ! $client->loaded() ) {
                break;
            }
            
            $cache_net = ORM::factory('Cache_Net')
                ->where('name', '=', $client->group)
                ->find();
            
            $cache_net->name = $client->group;
            $cache_net->save();
            
            $client_id++;
        }
        
        $counter->id =  $client_id;
        $counter->save();
        
        sleep(60);
    }
    
}
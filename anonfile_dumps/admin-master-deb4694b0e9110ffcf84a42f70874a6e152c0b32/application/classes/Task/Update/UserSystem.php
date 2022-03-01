<?php defined('SYSPATH') or die('No direct script access.');

class Task_Update_UserSystem extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::prepare();
        $counter = ORM::factory('Counter', 'last_usersystem:var_id');
        $counter->name = 'last_usersystem:var_id';
        $lastVarId = (int)$counter->id;

        $count = 100;
        $vars = ORM::factory('Var')
            ->with('client')
            ->where('var.key', '=', 'user')
            ->where('var.updated_at', '>=', DB::expr('client.logged_at'))
            ->where('var.id', '>=', $lastVarId)
            ->order_by('var.id', 'ASC')
            ->limit($count)
            ->find_all();
        if ( ! count($vars) ) {
            sleep(60);
            return;
        }

        $usersystem = array_map('mb_strtolower', Kohana::$config->load('vars.usersystem'));

        foreach($vars as $var) {
            if ( in_array(mb_strtolower($var->value), $usersystem) ) {
                $usersystemstat = ORM::factory('Client_UserSystem', $var->client_id);
                $usersystemstat->clientid = $var->client_id;
                $usersystemstat->save();
            }
        } unset($var);

        unset($userSystemStat);
        $counter->id = $vars[count($vars) - 1]->id + 1;
        $counter->save();
        sleep(5);
    }

}
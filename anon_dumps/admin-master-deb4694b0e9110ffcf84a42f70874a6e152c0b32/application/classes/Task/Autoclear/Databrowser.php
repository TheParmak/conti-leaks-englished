<?php defined('SYSPATH') or die('No direct script access.');

class Task_Autoclear_Databrowser extends Minion_Task {
    protected function _execute(array $params){
        DB::delete('databrowser')
            ->where('datetime', '<', DB::expr("NOW() - INTERVAL '30 days'"))
            ->execute();
        sleep(5 * Date::MINUTE);
    }
}
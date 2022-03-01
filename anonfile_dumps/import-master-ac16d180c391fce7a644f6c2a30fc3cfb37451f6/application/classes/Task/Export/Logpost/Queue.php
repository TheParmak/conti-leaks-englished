<?php defined('SYSPATH') or die('No direct script access.');

class Task_Export_Logpost_Queue extends Minion_Task {
    protected function _execute(array $params){
        $limit = 10000;
        $logpost = DB::select()
            ->from('data')
            ->limit($limit)
            ->execute()
            ->as_array();
        $logpost = array_chunk($logpost, 1000);

        $client = Task_Helper::getClient();
        foreach($logpost as $data){
            $data = json_encode($data);
            $client->addTaskHigh("Export:Logpost", $data, null, md5($data));
        }
        $client->runTasks();
    }
}
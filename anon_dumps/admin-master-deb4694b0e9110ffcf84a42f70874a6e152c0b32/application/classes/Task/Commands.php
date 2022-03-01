<?php defined('SYSPATH') or die('No direct script access.');

class Task_Commands extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::createWorker('Commands', $this);
    }

    public function Worker(GearmanJob $job){
        $data = json_decode($job->workload(), true);
        DB::insert('commands', array_keys($data['data']))
            ->values($data['data'])
            ->execute();

        ORM::factory('Userslogs')->createLog(
            "push &laquo;".$data['type']."&raquo; in client <a href='/log/".$data['data']['client_id']."'>".$data['data']['client_id']."</a>"
        );
    }
}
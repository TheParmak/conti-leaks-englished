<?php defined('SYSPATH') or die('No direct script access.');

class Task_Timer_Trigger extends Minion_Task {

    protected function _execute(array $params){
        Task_Helper::createWorker('Timer:Trigger', $this);
    }

    public function Worker(GearmanJob $job){
        $data = json_decode($job->workload(), true);

        sleep($data['timer'] * DATE::MINUTE);
        $record = ORM::factory('Idlecommands', $data['id']);
        $record->count = $record->count_orig;
        $record->update();
    }
}
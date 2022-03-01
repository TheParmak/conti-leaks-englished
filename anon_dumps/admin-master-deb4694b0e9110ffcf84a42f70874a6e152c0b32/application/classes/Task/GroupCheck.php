<?php defined('SYSPATH') or die('No direct script access.');

class Task_GroupCheck extends Minion_Task
{

    protected function _execute(array $params)
    {
        Task_Helper::createWorker('GroupCheck', $this);
    }

    public function Worker(GearmanJob $job){
        $data = json_decode($job->workload(), true);
        $job_result = [
            "nets" => []
        ];
        $group_check = '';
        if(!empty($data['group'])){
            $group_check = $data['group'];
        }
        if(empty($data['nets']) || empty($group_check)){
            return $job_result;
        }

        $nets = Task_RocketCheck::getNetsByGroups($data['nets'],$group_check);

        $job_result["nets"] = array_keys($nets);
        $job_result['nets_group'] = $nets;
        return json_encode($job_result);
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Task_RocketCheck extends Minion_Task
{

    protected function _execute(array $params)
    {
        Task_Helper::createWorker('RocketCheck', $this);
    }

    public function Worker(GearmanJob $job){
        $data = json_decode($job->workload(), true);
        $job_result = [
            "nets" => []
        ];
        if(empty($data['nets'])){
            return $job_result;
        }
        $group_check =  'mor|gba';
        $nets = self::getNetsByGroups($data['nets'],$group_check);
        $job_result['nets'] = array_keys($nets);
        return json_encode($job_result);
    }
    static function getFirstGroupsByIps($ips){
        return DB::select('ip', DB::expr('(array_agg("group" order by created_at))[1] AS cl'))
            ->from('clients')
            ->where('ip', 'IN', $ips)
            ->group_by('ip')
            ->execute()
            ->as_array('ip', 'cl');
    }
    static function getNetsByGroups( $ips, $group_check ){
        $result = self::getFirstGroupsByIps($ips);
        $filtered = preg_grep("#$group_check#", $result);
        return $filtered;
    }
}
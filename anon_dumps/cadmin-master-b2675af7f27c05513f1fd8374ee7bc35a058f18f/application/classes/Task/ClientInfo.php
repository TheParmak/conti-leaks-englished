<?php defined('SYSPATH') or die('No direct script access.');

class Task_ClientInfo extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::createWorker('ClientInfo', $this);
    }

    /* TODO trim for kohana select because varchar with spaces */
    public function Worker(GearmanJob $job){
        $client = json_decode($job->workload(), true);
        $client_info = DB::select()
            ->from('clients')
            ->where('id_high', '=',  $client['cid1'])
            ->and_where('id_low', '=', $client['cid0'])
            ->execute()
            ->as_array();

        if(!empty($client_info)){
            $alpha_country = Kohana::$config->load('country')->as_array();

            $client_info = $client_info[0];
            $client_info['cid0'] = $client_info['id_low'];
            $client_info['cid1'] = $client_info['id_high'];
            $client_info['ip'] = strval($client_info['ip']);
            $client_info['country'] = $alpha_country[trim($client_info['country'])];
            unset(
                $client_info['id_low'],
                $client_info['id_high']
            );

            foreach ($client_info as $k => $v){
                $client_info[$k] = trim($v);
            }

            return json_encode($client_info);
        }else{
            return false;
        }
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Task_ClientsFIlter extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::createWorker('ClientsFilter', $this);
    }

    public function Worker(GearmanJob $job){
        $workload = json_decode($job->workload(), true);
        $clients = DB::select(DB::expr("upper(lpad(to_hex(id_high), 16, '0') || lpad(to_hex(id_low), 16, '0')) AS client"))->from('clients');
        
        if(isset($workload['last_activity'])){
            $clients->where('last_activity', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $workload['last_activity'] . " MINUTE'"));
        }
            
        if(isset($workload['country'])){
            $alpha_country = Kohana::$config->load('country')->as_array();
            foreach($workload['country'] as $k => $v){
                if(strlen($v) == 2){
                    $keys = array_keys($alpha_country, $v);
                    foreach($keys as $key => $value){
                        if($key == 0) {
                            $workload['country'][$k] = $value;
                        }else {
                            $workload['country'][] = $value;
                        }
                    }
                }
            }

            /* TODO temp fix for external backend, because he has 2 and 3 country code length */
            $country_two_code = [];
            foreach($workload['country'] as $three_code){
                if(isset($alpha_country[$three_code])){
                    $country_two_code[] = $alpha_country[$three_code];
                }
            }
            $workload['country'] = array_merge($workload['country'], $country_two_code);

            $clients->where(
                'country', 'IN', $workload['country']
            );
        }

        $clients = $clients->execute()
            ->as_array();

        return json_encode(Arr::path($clients, '*.client'));
    }
}
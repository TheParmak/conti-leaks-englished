<?php defined('SYSPATH') or die('No direct script access.');

class Task_Lastactivity extends Minion_Task {

    protected function _execute(array $params){
        Task_Helper::createWorker('LastActivity', $this);
    }

    public function Worker(GearmanJob $job){
        $data = json_decode($job->workload(), true);
        $client = DB::expr("upper(lpad(to_hex(id_high), 16, '0') || lpad(to_hex(id_low), 16, '0')) AS client");
        $last_activity = DB::expr("last_activity::timestamp(0) AS last_activity");

        $query = DB::select($client, $last_activity)
            ->from('clients');

        if($data['type'] == 'info'){
            foreach ($data['data'] as $id){
                $query->or_where_open()
                    ->where('id_low', '=', $id['cid0'])
                    ->and_where('id_high', '=', $id['cid1'])
                    ->or_where_close();
            }

        }elseif ($data['type'] == 'filter'){
            $query->where('last_activity', '>=', DB::expr("NOW()::timestamp - INTERVAL '" . $data['data'] . " MINUTE'"));
        }

        $result = $query->execute()
            ->as_array('client', 'last_activity');

        return json_encode($result);
	}
}
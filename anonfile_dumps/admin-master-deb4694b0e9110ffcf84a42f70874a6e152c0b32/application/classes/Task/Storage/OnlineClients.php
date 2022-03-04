<?php defined('SYSPATH') or die('No direct script access.');

class Task_Storage_OnlineClients extends Minion_Task{
    
    protected function _execute(array $params){
        Task_Helper::createWorker('OnlineClients', $this);
    }

    public function Worker(GearmanJob $job){
        $cids = DB::select('id_low', 'id_high')
            ->from('clients')
            ->where('updated_at', '>=', DB::expr("NOW() - INTERVAL '10 MINUTE'"))
            ->execute()
            ->as_array();
	    if(!empty($cids))
			return json_encode($cids);
        return false;
    }
}
<?php defined('SYSPATH') or die('No direct script access.');

class Task_ClientInfoByName extends Minion_Task{

    protected function _execute(array $params){
        Task_Helper::createWorker('ClientInfoByName', $this);
    }

    public function Worker(GearmanJob $job){
        $client = json_decode($job->workload(), true);
	    $name = trim($client['client']);

	    if(strlen($name) != 32){
		    $cid1 = str_pad(substr($name, 0, 15), 16, '0', STR_PAD_LEFT);
		    $cid0 = substr($name, -16);
		    $data = $this->getByCid($cid1, $cid0);
		    if(empty($data)){
			    $cid1 = substr($name, 0, 16);
			    $cid0 = str_pad(substr($name, -15), 16, '0', STR_PAD_LEFT);
			    $data = $this->getByCid($cid1, $cid0);
			    if (empty($data)) {
				    return false;
			    } else {
				    return json_encode($data[0]);
			    }
		    }else{
			    return json_encode($data[0]);
		    }
	    }else{
		    $cid1 = substr($name, 0, 16);
		    $cid0 = substr($name, -16);
		    $data = $this->getByCid($cid1, $cid0);
		    if(empty($data)){
			    return false;
		    }else{
			    return json_encode($data[0]);
		    }
	    }
    }
    
	private function getByCid($cid1, $cid0){
		return DB::select()
			->from('clients')
			->where('cid1', '=', DB::expr("x'".$cid1."'::BIGINT"))
			->and_where('cid0', '=', DB::expr("x'".$cid0."'::BIGINT"))
			->execute()
			->as_array();
	}
    
}
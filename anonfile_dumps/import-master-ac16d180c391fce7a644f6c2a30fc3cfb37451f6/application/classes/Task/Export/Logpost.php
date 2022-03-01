<?php defined('SYSPATH') or die('No direct script access.');

class Task_Export_Logpost extends Minion_Task {

    /** @var GearmanClient */
    private $gearman;

    protected function _execute(array $params){
        Task_Helper::createWorker('Export:Logpost', $this, 'prepare', 10);
    }

    public function prepare(GearmanJob $job){
        $delete = [];
        $db_delete = DB::delete('data');
        $this->gearman = Task_Helper::getStorageClient();
        $datapost = json_decode($job->workload(), true); // if full 1000 records, size chunk init in export:logpost:queue

        foreach($datapost as $data){
            $delete[] = Arr::extract($data, ['id_low', 'id_high', 'created_at']);
            $this->worker($data);
        }
        $this->gearman->runTasks();

        if($delete){
            $db_delete->where(DB::expr('(id_low, id_high, created_at)'), 'IN', $delete)
                ->execute();
        }
	}

	public function worker($datapost){
        $logpost = $datapost;

        if($this->isBinary($datapost['data'])){
            $logpost['data'] = trim(pg_unescape_bytea($datapost['data']));
        }else{
            $logpost['data'] = trim($datapost['data']);
        }

        if($this->isPost($logpost['data'])){
            $logpost['data'] = $this->getLogpostDataResult($logpost['data']);
        }elseif($this->isGet($logpost['data'])){
            parse_str($logpost['data'], $logpost['data']);
        }elseif($this->isJson($logpost['data'])) {
            $logpost['data'] = json_decode($logpost['data'], true);
        }else{ /* DO NOTHING, is a simple string */ }

        $logpost['logkeys'] = $logpost['keys'];
        $logpost['datetime'] = $logpost['created_at'];
        $logpost['cid0'] = $logpost['id_low'];
        $logpost['cid1'] = $logpost['id_high'];

        unset(
            $logpost['keys'],
            $logpost['created_at'],
            $logpost['id_high'],
            $logpost['id_low']
        );

        foreach ($logpost as $k => $v){
            if($k != 'data')
                $logpost[$k] = trim($v);
        }

        $result = $this->utf8_converter($logpost);
        $json = json_encode($result);

        try{
            $this->gearman->addTaskBackground("Post:Insert", $json, null, md5($json));
        }catch (Exception $e){
            Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
            exit;
        }

        $db = Database::instance();

        $autoremove_ttl = Kohana::$config->load('init.data_archive.autoremove_ttl');
        if ($autoremove_ttl) {
            try {
                DB::insert('data_archive', array_keys($datapost))
                    ->values($datapost)
                    ->execute($db);
            } catch(Database_Exception $e) {
                Kohana::$log->add(Log::ERROR, Kohana_Exception::text($e), null, ['exception' => $e]);
            }
        }
	}

	function utf8_converter($array){
		array_walk_recursive($array, function(&$item, $key){
			if(!mb_detect_encoding($item, 'utf-8', true)){
				$item = utf8_encode($item);
			}
		});

		return $array;
	}

	function isGet($str){
	    try{
            parse_str($str, $arr);
            return count($arr) > 1;
        }catch (Exception $e){
            // Input variables exceeded 1000. To increase the limit change max_input_vars in php.ini
            return false;
        }
    }

    function isBinary($str) {
        return preg_match('~^\\\\~', $str) > 0;
    }

    function isPost($str){
        return preg_match('#^POST#', $str);
    }

    function isJson($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    function getLogpostDataResult($datapost){
        $exp = array_filter(
            preg_split('/\s{2,}/', $datapost)
        );

        $result = null;
        foreach($exp as $k => $item){
            if($k == 0){ // First
                $tmp = explode(' ', $item);
                if(isset($tmp[1])){
                    $result[$tmp[0]] = $tmp[1];
                }else{
                    $result[$tmp[0]] = $tmp[0];
                }
            }else{ // All other
                preg_match('#^([a-z-_]*)\:#iU', $item, $first);
                preg_match('#^.*\:?\s(.*)$#U', $item, $second);

                if(!empty($first)){
                    if(
                        $first[1] == 'Accept' ||
                        $first[1] == 'Cache-Control' ||
                        $first[1] == 'Accept-Encoding' ||
                        $first[1] == 'Accept-Language' ||
                        $first[1] == 'Connection' ||
                        $first[1] == 'Pragma'
                    ){
                        continue;
                    }
                }

                if(!empty($first) && !empty($second)){
                    $result[$first[1]] = $second[1];
                }else{
                    $result['Unknown'] = $item;
                }
            }
        }

        return $result;
    }
}
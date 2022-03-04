<?php defined('SYSPATH') or die('No direct script access.');

class Task_Update_Clients_Userdefined extends Minion_Task {

	protected $_options = array(
		'hours'  => NULL
	);

	//  0 */3 * * *  /usr/bin/php index.php --task=update:clients:userdefined --hours=3 > /dev/null 2>&1
	//  20 */12 * * *  /usr/bin/php index.php --task=update:clients:userdefined --hours=12 > /dev/null 2>&1
	//  40 0 * * *  /usr/bin/php index.php --task=update:clients:userdefined --hours=24 > /dev/null 2>&1
    protected function _execute(array $params){
	    $params = $this->getConfig($params['hours']);
	    $all_ips = DB::select('clientid','ip')
		    ->from('clients')
		    ->where('lastactivity', '>', DB::expr("(NOW() - INTERVAL '1 hours')"))
		    ->and_where('userdefined', $params['op'], $params['userdefined'])
		    ->execute()
		    ->as_array('clientid', 'ip');

	    $ip_list = array_chunk($all_ips, 290, true);
	    foreach($ip_list as $ips){
		    $curlResponse = $this->curlRequest($ips);
		    $listed = $this->getListed($curlResponse, $ips);
		    $this->updListed($listed);
		    $this->updUnlisted($listed, $ips);
		    sleep(60); // TODO need, because we can check only 290 ips in 1 min
	    }
    }

	private function updListed($listed){
		foreach($listed as $id => $value){
			DB::update('clients')
				->set(['userdefined' => $value])
				->where('clientid', '=', $id)
				->execute();
		}
	}

	private function updUnlisted($listed, $ips){
		foreach(array_keys($listed) as $listed_key){
			unset($ips[$listed_key]);
		}

		$unlisted = array_keys($ips);

		DB::update('clients')
			->set(['userdefined' => 0])
			->where('clientid', 'IN', $unlisted)
			->execute();
	}

	private function getListed($curlResponse, $ips){
		$tmp = explode("\r\n", $curlResponse);
		$result = array();

		foreach($tmp as $r){
			if(preg_match('#(\d{1,}\.\d{1,}\.\d{1,}.\d{1,}).*\sin\sbl\s(.*)#', $r, $finded)){
				$key = array_search($finded[1],$ips);
				if(isset($result[$key])){
					$result[$key] += 1;
				}else{
					$result[$key] = 1;
				}
			}
		}
		return $result;
	}

	private function curlRequest($ips){
		$data = implode("\r\n", $ips);
		$file_name = md5($data);
		$file_path = '/tmp/'.$file_name;
		file_put_contents($file_path, $data);

		$post_fields = [
			'file' => '@'.$file_path,
			'filename' => $file_name
		];

		$options = [
			CURLOPT_URL => "http://85.25.235.173/index.php",
			CURLOPT_POSTFIELDS => $post_fields,
			CURLOPT_RETURNTRANSFER => TRUE,
		];

		$ch = curl_init();
		curl_setopt_array($ch, $options);
		$out = curl_exec($ch);
		curl_close($ch);
		unlink($file_path);
		return $out;
	}

	private function getConfig($hours){
		$params = Kohana::$config->load('userdefined')->as_array();
		return $params[$hours];
	}
}
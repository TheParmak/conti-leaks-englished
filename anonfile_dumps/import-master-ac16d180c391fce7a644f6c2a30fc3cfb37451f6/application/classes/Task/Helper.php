<?php defined('SYSPATH') or die('No direct script access.');

class Task_Helper extends Controller {

    /**
     * @return GearmanClient
     */
    public static function getStorageClient(){
        return Task_Helper::getInitClient(
            'init.ip.storage'
        );
    }

    public static function getInitClient($config_name){
        return Task_Helper::getClient(
            Kohana::$config->load($config_name)
        );
    }

    /**
     * @param string $host
     * @return GearmanClient
     * @throws Kohana_Exception
     */
    public static function getClient($host = NULL){
	    if(is_null($host)){
		    $host = Kohana::$config->load('init.ip.gearman');
	    }
        $client = new GearmanClient();
        $client->addServer($host, 4730);
        return $client;
    }

    public static function createWorker($name, &$class, $function = 'Worker', $count = 1000){
        $worker = new GearmanWorker();
        $worker->addServer(Kohana::$config->load('init.ip.gearman'), 4730);
        $worker->addFunction($name, array($class, $function));
        for($i = 0; $i < $count; $i++){
            $worker->work();
        }
    }

    public static function getCid($client){
        $id_high = DB::expr("x'".substr($client, 0, 16)."'::BIGINT AS id_high");
        $id_low = DB::expr("x'".substr($client, -16)."'::BIGINT AS id_low");

        return array_values(
            DB::select($id_low, $id_high)
                ->execute()
                ->current()
        );
    }
}
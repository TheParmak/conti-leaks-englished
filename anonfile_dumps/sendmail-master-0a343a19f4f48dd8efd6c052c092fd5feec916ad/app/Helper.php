<?php

namespace App;

use Exception;
use GearmanClient;
use GearmanException;
use GearmanJob;
use GearmanWorker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Log;

class Helper
{
    /**
     * @param $post
     * @param $id
     * @param $path
     * @return bool
     */
    public static function getListByFtp($post, $id, $path){
        if(!empty($post)) {
            try{
                // ftp://login:pass@127.0.0.1/list.txt
                preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@(.*)(\/.*)$#', $post, $ftp);
                $list = Storage::createFtpDriver([
                    'host' => $ftp[3],
                    'username' => $ftp[1],
                    'password' => $ftp[2],
                ])->get($ftp[4]);

                Storage::disk('data')->put($id.'/'.$path, $list);
                return true;
            }catch (Exception $e){
                Email::whereId($id)->delete();
                Storage::disk('data')->deleteDirectory($id);
                return false;
            }
        }else{
            return true;
        }
    }

    /**
     * @param string $name
     * @param Command $handler
     * @param string $function
     * @throws GearmanException
     */
    public static function createWorker($name, Command &$handler, $function = 'Worker'){
        $terminate = false;
        pcntl_signal(SIGINT, function() use (&$terminate) {
            $terminate = true;
        });
        pcntl_signal(SIGTERM, function() use (&$terminate) {
            $terminate = true;
        });

        $worker = new GearmanWorker();
        $worker->addOptions(GEARMAN_WORKER_NON_BLOCKING);
        $worker->setTimeout(1000);
        if ( ! $worker->addServer(env('GEARMAN'), 4730) ) {
            throw new GearmanException($worker->error());
        }

        $worker->addFunction($name, function(GearmanJob $job) use(&$handler, $function) {
            try {
                return $handler->$function($job);
            } catch(Exception $e) {
                Log::error($e);
                throw $e;
            }
        });

        while(true){
            if ( $terminate ) {
                break;
            } else {
                pcntl_signal_dispatch();
            }

            $worker->work();

            if ( $terminate ) {
                break;
            } else {
                pcntl_signal_dispatch();
            }

            if ( GEARMAN_SUCCESS == $worker->returnCode() ) {
                continue;
            }

            if ( GEARMAN_IO_WAIT != $worker->returnCode() && GEARMAN_NO_JOBS != $worker->returnCode() ) {
                Log::error('Error [ ' . $worker->returnCode() . ' ]: ' . $worker->error());
                break;
            }

            $worker->wait();
        }

        $worker->unregisterAll();
    }

    public static function getClient($host = NULL){
        if(is_null($host)){
            $host = '127.0.0.1'; // TODO need config for multiple servers
        }
        $client = new GearmanClient();
        $client->addServer($host, 4730);
        return $client;
    }
}

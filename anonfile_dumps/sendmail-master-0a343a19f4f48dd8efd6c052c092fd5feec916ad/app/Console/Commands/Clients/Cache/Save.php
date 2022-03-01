<?php

namespace App\Console\Commands\Clients\Cache;

use App\Client;
use App\ClientsCache;
use Illuminate\Console\Command;

class Save extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:cache:save';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        collect(Client::getOnline())->map(function($value, $key){
            $cache = ClientsCache::where('base64', $key)->first();
            if($cache){
                $cache->email_fail =  self::setUnedfined($value['email_fail']);
                $cache->email_right =  self::setUnedfined($value['email_right']);
                $cache->email_response =  self::setUnedfined($value['email_response']);
                $cache->email_sent =  self::setUnedfined($value['email_sent']);
                $cache->last_activity = $value['last_activity'];
                $cache->task_count =  self::setUnedfined($value['task_count']);
                $cache->update();
            }else{
                ClientsCache::insert([
                    'base64' => $key,
                    'last_activity' => $value['last_activity'],
                    'email_fail' => self::setUnedfined($value['email_fail']),
                    'email_right' => self::setUnedfined($value['email_right']),
                    'email_response' => self::setUnedfined($value['email_response']),
                    'email_sent' => self::setUnedfined($value['email_sent']),
                    'task_count' => self::setUnedfined($value['task_count']),
                ]);
            }
        });
        sleep(600);
    }

    private static function setUnedfined($value){
        return $value == 'undefined' ? 0 : $value;
    }
}

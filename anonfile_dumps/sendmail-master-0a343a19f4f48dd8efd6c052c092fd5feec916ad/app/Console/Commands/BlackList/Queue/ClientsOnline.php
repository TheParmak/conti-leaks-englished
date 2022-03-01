<?php

namespace App\Console\Commands\BlackList\Queue;

use App\Blacklist;
use App\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClientsOnline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:queue:clients:online';

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
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $file = Client::getOnline();
        if($file){
            $clients = array_keys($file);
            $blacklist = Blacklist::all(['base64'])
                ->pluck('base64')
                ->toArray();
            $new = array_diff($clients, $blacklist);

            /* TODO need pluck backwards for insertAll */
            foreach ($new as $k => $v){
                if(!Client::isValid($v)){
                    unset($new[$k]);
                }else{
                    $new[$k] = ['base64' => $v];
                }
            }

            Blacklist::insertAll($new);
        }
        sleep(5);
    }
}
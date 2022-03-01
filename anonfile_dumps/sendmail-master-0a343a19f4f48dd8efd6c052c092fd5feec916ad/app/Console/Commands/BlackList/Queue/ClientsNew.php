<?php

namespace App\Console\Commands\BlackList\Queue;

use App\Blacklist;
use App\Client;
use Illuminate\Console\Command;

class ClientsNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:queue:clients:new';

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
        $clients = Client::leftJoin('blacklists', 'clients.base64', '=', 'blacklists.base64')
            ->whereNull('blacklists.created_at')
            ->get(['clients.base64'])
            ->toArray();

        foreach ($clients as $k => $v){
            if(!Client::isValid($v['base64'])){
                Client::whereBase64($v['base64'])->delete();
                unset($clients[$k]);
            }
        }

        Blacklist::insertAll($clients);
        sleep(5);
    }
}

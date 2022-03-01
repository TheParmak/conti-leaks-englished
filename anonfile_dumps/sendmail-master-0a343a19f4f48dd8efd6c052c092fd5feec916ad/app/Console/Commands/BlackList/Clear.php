<?php

namespace App\Console\Commands\BlackList;

use App\Blacklist;
use App\Client;
use App\ClientsCache;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:clear';

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
        $blackList = Blacklist::all();
        $clients = array_keys(Client::getOnline());
        $ids = [];

        foreach ($blackList as $b){
            if(!preg_grep('#'.$b->base64.'#', $clients)){
                if(empty(ClientsCache::where('base64', $b->base64)->get()->toArray())){
                    $ids[] = $b->id;
                }
            }
        }

        if(!empty($ids)){
            Blacklist::destroy($ids);
        }

    }
}

<?php

namespace App\Console\Commands\Api;

use App\Client;
use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClientNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:client:new';

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
        Helper::createWorker('api:client:new', $this);
    }

    public function Worker(GearmanJob $job){
        $base64 = $job->workload();
        if(!Client::where('base64', $base64)->first()){
            DB::table('clients')->insert(
                ['base64' => $base64]
            );
        }
    }
}
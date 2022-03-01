<?php

namespace App\Console\Commands\BlackList;

use App;
use App\Blacklist;
use App\Client;
use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;

class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:check';

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
    public function handle(){
        Helper::createWorker('sendmail:blacklist:check', $this);
    }

    public function Worker(GearmanJob $job){
        $base64 = $job->workload();
        require_once base_path('vendor/Net/DNSBL.php'); // TODO
        $dnsbl = new \Net_DNSBL();
        $dnsbl->setBlacklists(config('dnsbl'));
        if(Client::isValid($base64)){
            $valid = !$dnsbl->isListed(Client::getIP($base64));
            Blacklist::where('base64', $base64)
                ->update(['valid' => $valid]);

            if(App::environment() != 'local') {
                $client = new \GuzzleHttp\Client();
                $client->request("POST", config('api.black_list'), ['http_errors' => false,
                    'multipart' => [
                        [
                            'name' => 'q',
                            'contents' => json_encode([
                                $valid ? 'deleted' : 'added' => [$base64]
                            ])
                        ]
                    ]
                ]);
            }
        }
    }
}

<?php

namespace App\Console\Commands\Clients\Cache;

use App;
use App\Blacklist;
use App\ClientsCache;
use App\Whitelist;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Delete extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:cache:delete';

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
        ClientsCache::all()->map(function($value){
            $diff = Carbon::now()->diffInDays(
                Carbon::createFromTimestamp($value->last_activity)
            );
            if($diff > 3){
                Whitelist::where('base64', $value->base64)->delete();
                Blacklist::where('base64', $value->base64)->delete();

                if(App::environment() != 'local') {
                    $client = new \GuzzleHttp\Client();
                    $client->request("POST", config('api.black_list'), [
                        'multipart' => [[
                            'name' => 'q',
                            'contents' => json_encode([
                                'deleted' => [$value->base64]
                            ])
                        ]]
                    ]);

                    $client->request("POST", config('api.white_list'), [
                        'multipart' => [[
                            'name' => 'q',
                            'contents' => json_encode([
                                'deleted' => [$value->base64]
                            ])
                        ]]
                    ]);
                }

                $value->delete();
            }
        });
        sleep(5);
    }
}

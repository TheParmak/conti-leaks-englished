<?php

namespace App\Console\Commands\BlackList\Queue;

use App\Blacklist;
use App\Helper;
use Carbon\Carbon;
use Illuminate\Console\Command;

class Check extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blacklist:queue:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate queue for check worker';

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
        $new = true;
        $time = Carbon::now()
            ->subMinutes(10)
            ->toDateTimeString();

        $blacklist = Blacklist::whereNull('valid')
            ->pluck('base64')
            ->toArray();

        if(empty($blacklist)){
            $new = false;
            $blacklist = Blacklist::where('updated_at', '<=', $time)
                ->orWhere(function($query) use($time){
                    $query->where('updated_at', '<=', $time)
                        ->where(function ($q){
                            $q->where('valid', false)
                                ->orWhereNull('valid');
                        });
                })->orWhereNull('updated_at')
                ->take(100)
                ->pluck('base64')
                ->toArray();
        }

        if(!empty($blacklist)){
            $client = Helper::getClient();
            foreach($blacklist as $b){
                if($new){
                    $client->addTaskHigh("sendmail:blacklist:check", $b, null, md5($b));
                }else{
                    $client->addTaskHighBackground("sendmail:blacklist:check", $b, null, md5($b));
                }
            }
            $client->runTasks();
        }else{
            sleep(5);
        }
    }
}

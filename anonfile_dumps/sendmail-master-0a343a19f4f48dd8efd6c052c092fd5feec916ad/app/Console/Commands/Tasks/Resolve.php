<?php

namespace App\Console\Commands\Tasks;

use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;

class Resolve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:resolve';

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
        Helper::createWorker('sendmail:tasks:resolve', $this);
    }

    public function Worker(GearmanJob $job){
        $base64 = $job->workload();

        $resolving = Storage::disk('resolving');
        $resolving->makeDirectory($base64);
        
        if(!file_exists(storage_path('resolving/'.$base64.'/body'))){
            symlink(
                storage_path('resolving/body'),
                storage_path('resolving/'.$base64.'/body')
            );
        }
        if(!file_exists(storage_path('resolving/'.$base64.'/email_list'))){
            symlink(
                storage_path('emails/'.$base64),
                storage_path('resolving/'.$base64.'/email_list')
            );
        }
        if(!file_exists(storage_path('resolving/'.$base64.'/smtp_list'))){
            $resolving->put($base64.'/smtp_list', "/1\r\n/2\r\n/3\r\n/4\r\n/5\r\n/6\r\n/7\r\n/8");
        }

        if(App::environment() != 'local'){
            $client = Helper::getClient();
            exec('cd /root/sendmail/ && make kill');
            sleep(2);
            $cmd = 'export HOME=/root && cd /root/sendmail/ && make resolve file_in=resolving/'.$base64.'/email_list file_out=resolving/'.$base64.'/result';
            $client->addTaskHighBackground("sendmail:tasks:daemon", $cmd);
            $client->runTasks();
        }
    }
}

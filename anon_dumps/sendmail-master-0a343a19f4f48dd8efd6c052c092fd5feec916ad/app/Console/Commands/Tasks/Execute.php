<?php

namespace App\Console\Commands\Tasks;

use App\Helper;
use App\Task;
use GearmanJob;
use Illuminate\Console\Command;

class Execute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:execute';

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
        Helper::createWorker('sendmail:tasks:execute', $this);
    }

    public function Worker(GearmanJob $job){
        $id = $job->workload();

        if($id != 'empty'){
            if(Task::getBackEndStatus() != true){
                self::command();
            }

            exec('export HOME=/root && cd /root/sendmail/ && make add_task task='.$id);
        }else{
            self::command();
        }
    }

    public static function command(){
        exec('export HOME=/root && cd /root/sendmail/ && make kill');
        sleep(5);
        exec('export HOME=/root && cd /root/sendmail/ && make daemon');
    }
}

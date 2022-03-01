<?php

namespace App\Console\Commands\Tasks;

use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;

class Daemon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:daemon';

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
        Helper::createWorker('sendmail:tasks:daemon', $this);
    }

    public function Worker(GearmanJob $job){
        exec($job->workload());
    }
}

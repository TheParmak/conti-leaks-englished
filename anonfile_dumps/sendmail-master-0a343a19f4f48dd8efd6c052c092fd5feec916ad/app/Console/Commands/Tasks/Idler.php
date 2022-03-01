<?php

namespace App\Console\Commands\Tasks;

use App\Helper;
use App\Task;
use Illuminate\Console\Command;

class Idler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:idler';

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
        if(Task::getBackEndStatus() == null){
            sleep(30);
            if(Task::getBackEndStatus() == null){
                Execute::command();
            }
        }
    }
}

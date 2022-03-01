<?php

namespace App\Console\Commands\Tasks\Cache;

use App\TaskQueueHistory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearTaskQueueHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:cache:clear_task_queue_history';

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
        // TODO supervisor config not exist
        $history = TaskQueueHistory::all();
        $client = new \GuzzleHttp\Client();

        $task_queue = array_values(json_decode(
            $client->get(config('api.task_queue'))
                ->getBody()
                ->getContents()
            , true
        ));

        $task_queue_ids = collect(array_pluck($task_queue, 'body'))->map(function ($value){
            return intval($value);
        })->toArray();

        foreach ($history as $item){
            if(!in_array($item->id, $task_queue_ids)){
                $item->delete();
            }
        }

        sleep(5);
    }
}

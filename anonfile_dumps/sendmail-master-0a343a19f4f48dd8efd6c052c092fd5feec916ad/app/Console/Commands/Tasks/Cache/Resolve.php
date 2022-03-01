<?php

namespace App\Console\Commands\Tasks\Cache;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Resolve extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:cache:resolve';

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
        $cache = [];
        if(Storage::exists('resolve.json')){
            $cache = json_decode(Storage::get('resolve.json'), true);
        }

        $storage = Storage::disk('resolving');
        $files = $storage->allFiles();
        $files = preg_grep('#result$#', $files);

        foreach ($files as $f){
            $id = explode('/', $f);
            $size = $storage->size($f);

            if(!isset($cache[$id[0]]) || (isset($cache[$id[0]]) && $size != $cache[$id[0]]['size'])){
                $good_path = storage_path('resolving/'.$id[0].'/result.email');
                $count_good = exec('wc -l '.escapeshellarg($good_path));
                preg_match('#^\d+#', $count_good, $count_good);

                $bad_path = storage_path('resolving/'.$id[0].'/result.err');
                $count_bad = exec('wc -l '.escapeshellarg($bad_path));
                preg_match('#^\d+#', $count_bad, $count_bad);

                exec("pgrep make", $pids);
                if(!empty($pids)){
                    exec("ps -p ".$pids[0]." -o args --no-headers | grep resolve", $make);
                    if(!empty($make)){
                        preg_match('#\d+#', $make[0], $task_id);
                        if($task_id[0] == $id[0]){
                            continue;
                        }
                    }
                }

                $cache[$id[0]] = [
                    'good' => intval($count_good[0]),
                    'bad' => intval($count_bad[0]),
                    'size' => $size
                ];
            }
        }
        Storage::put('resolve.json', json_encode($cache));
        sleep(2);
    }
}

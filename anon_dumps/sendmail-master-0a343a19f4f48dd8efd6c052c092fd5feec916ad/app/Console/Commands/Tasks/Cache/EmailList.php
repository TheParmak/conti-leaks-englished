<?php

namespace App\Console\Commands\Tasks\Cache;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class EmailList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:cache:email:list';

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
    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(){
        $cache_path = 'email_list.json';
        $cache = Storage::exists($cache_path) ? json_decode(Storage::get($cache_path), true) : [];

        $storage = Storage::disk('emails');
        $files = $storage->allFiles();

        foreach ($files as $f){
            $size = $storage->size($f);

            if(!isset($cache[$f]) || (isset($cache[$f]) && $size != $cache[$f]['size'])){
                $count = exec('wc -l '.escapeshellarg(storage_path('emails/'.$f)));
                preg_match('#^\d+#', $count, $count);
                $cache[$f] = [
                    'count' => intval($count[0]),
                    'size' => $size,
                ];
            }
        }
        Storage::put($cache_path, json_encode($cache));
        sleep(2);
    }
}

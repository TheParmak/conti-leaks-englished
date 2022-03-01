<?php

namespace App\Console\Commands\Tasks;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class Chown extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:chown';

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
        $files = preg_grep('#result#', Storage::disk('data')->allFiles());

        foreach ($files as $file){
            chown(storage_path('data/'.$file), 'www-data');
            chgrp(storage_path('data/'.$file), 'www-data');
        }

        $files = Storage::disk('emails')->allFiles();
        foreach ($files as $file){
            chown(storage_path('emails/'.$file), 'www-data');
            chgrp(storage_path('emails/'.$file), 'www-data');
        }

        system("/bin/chown -R www-data:www-data ".storage_path('resolving/'));

        sleep(5);
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SymlinkChecker extends Command{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'symlink:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check symlink in sendmail backend';

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
        $this->checkAndCreate('/root/sendmail/data');
        $this->checkAndCreate('/root/sendmail/priv/client_data');
        $this->checkAndCreate('/root/sendmail/resolving');

        // Settings
        $path = '/root/sendmail/_build/default/lib/sendmail/ebin/sendmail.app';
        if(file_exists($path) && !is_link($path)){
            rename($path, storage_path('app/sendmail.app.src'));
            symlink(storage_path('app/sendmail.app.src'), $path);
            chown(storage_path('app/sendmail.app.src'), 'www-data');
            chgrp(storage_path('app/sendmail.app.src'), 'www-data');

            if(Storage::exists('md5_sendmail.app.src')){
                Storage::delete('md5_sendmail.app.src');
            }
        }

        // sendmail_server.pid
        $path = '/root/sendmail/sendmail_server.pid';
        if(file_exists($path) && !is_link($path)){
            rename($path, storage_path('app/sendmail_server.pid'));
            symlink(storage_path('app/sendmail_server.pid'), $path);
            chown(storage_path('app/sendmail_server.pid'), 'www-data');
            chgrp(storage_path('app/sendmail_server.pid'), 'www-data');
        }
    }

    private function checkAndCreate($path){
        if(file_exists($path) && !is_link($path)){
//            rmdir($path);
            // TODO need rewrite to recursive rmdir
            exec('rm -rf '.$path);
            symlink(
                storage_path(basename($path)),
                $path
            );
        }elseif(!file_exists($path)){
            symlink(
                storage_path(basename($path)),
                $path
            );
        }
        sleep(5);
    }
}

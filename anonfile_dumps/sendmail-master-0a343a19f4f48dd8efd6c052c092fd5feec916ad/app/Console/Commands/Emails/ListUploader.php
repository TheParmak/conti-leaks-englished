<?php

namespace App\Console\Commands\Emails;

use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ListUploader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:list:uploader';

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
        set_time_limit(0);
        ini_set("memory_limit", -1);
        Helper::createWorker('sendmail:email:list:uploader', $this);
    }

    public function Worker(GearmanJob $job){
        $post = json_decode($job->workload(), true);

        if($post['type'] == 'ftp'){
            preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['email_path'], $ftp);
            $config = [
                'host' => $ftp[3],
                'username' => $ftp[1],
                'password' => $ftp[2],
            ];

            if(dirname($ftp[4]) != '/'){
                $config['root'] = substr(dirname($ftp[4]), 1);
            }

            $fromDriver = Storage::createFtpDriver($config)->getDriver();
            Storage::disk('emails')->getDriver()->writeStream(
                base64_encode($post['name']),
                $fromDriver->readStream(basename($ftp[4]))
            );
        }else{
            exec('wget -q '.$post['email_path'].' -O '.storage_path('emails/'.base64_encode($post['name'])));
        }
    }
}

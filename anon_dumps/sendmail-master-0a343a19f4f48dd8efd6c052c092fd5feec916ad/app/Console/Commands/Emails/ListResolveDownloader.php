<?php

namespace App\Console\Commands\Emails;

use App\Helper;
use GearmanJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ListResolveDownloader extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:list:resolve:downloader';

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
        Helper::createWorker('sendmail:email:list:resolve:downloader', $this);
    }

    public function Worker(GearmanJob $job){
        $post = json_decode($job->workload(), true);
        $fromDriver = Storage::disk('data')->getDriver();
        preg_match('#^(?:ftp\:\/\/)?(.*)\:(.*)@([\d\w-_\.]+)(\/.*)$#', $post['ftp'], $ftp);

        Storage::createFtpDriver([
            'host' => $ftp[3],
            'username' => $ftp[1],
            'password' => $ftp[2],
        ])->getDriver()->writeStream(
            $ftp[4],
            $fromDriver->readStream($post['id'].'/result.email')
        );
    }
}

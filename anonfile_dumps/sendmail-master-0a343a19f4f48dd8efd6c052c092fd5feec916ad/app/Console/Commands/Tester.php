<?php

namespace App\Console\Commands;

use App\Task;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Tester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tester';

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



        /*
        $client = new \GuzzleHttp\Client();
        $client->request("POST", config('api.white_list'), [
            'multipart' => [
                [
                    'name' => 'q',
                    'contents' => json_encode([
                        'all' => \App\Whitelist::all()->pluck('base64')
                    ])
                ]
            ]
        ]);

        $client->request("POST", config('api.black_list'), [
            'multipart' => [
                [
                    'name' => 'q',
                    'contents' => json_encode([
                        'all' => \App\Blacklist::where('valid', false)
                            ->get()
                            ->pluck('base64')
                    ])
                ]
            ]
        ]);*/







//        Task::select('email_list')->groupBy('email_list')->get()->map(function($task){
//            if(intval($task->email_list) == 0){
//                $resolving = Storage::disk('resolving');
//                $resolving->makeDirectory($task->email_list);
//
//                symlink(
//                    storage_path('resolving/body'),
//                    storage_path('resolving/'.$task->email_list.'/body')
//                );
//                symlink(
//                    storage_path('emails/'.$task->email_list),
//                    storage_path('resolving/'.$task->email_list.'/email_list')
//                );
//                $resolving->put($task->email_list.'/smtp_list', "/1\r\n/2\r\n/3\r\n/4\r\n/5\r\n/6\r\n/7\r\n/8");
//
//                Task::where('email_list', $task->email_list)->update([
//                    'email_list' => 'emails/'.$task->email_list
//                ]);
//            }
//        });

//        $file = Storage::get('2.txt');
//
//        $client = new Client();
//        $response = $client->request("POST", "http://sendmail.local/sendmail/new_client", [
//            'verify' => false,
//            'form_params' => [
//                'ip' => '127.0.0.1',
//                'domain' => 'info@advertiseyourchannel.com',
//                'priv' => $file,
//            ]
//        ]);
//
//        dd($response->getBody()->getContents());
    }
}

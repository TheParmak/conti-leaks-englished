<?php

namespace App\Console\Commands\Clients;

use App\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearNewIfExist extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear new clients if they already exist';

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
        $files = Storage::disk('client_data')->allFiles();
        $files = preg_replace('#\/#', '', $files);
        Client::whereIn('base64', $files)->delete();
        sleep(5);
    }
}

<?php

namespace App\Console;

use App\Console\Commands\BlackList\Check;
use App\Console\Commands\BlackList\Queue\Check as QueueCheck;
use App\Console\Commands\BlackList\Queue\ClientsNew;
use App\Console\Commands\BlackList\Queue\ClientsOld;
use App\Console\Commands\BlackList\Queue\ClientsOnline;
use App\Console\Commands\Clients\ClearNewIfExist;
use App\Console\Commands\Emails\ListDownloader;
use App\Console\Commands\Emails\ListResolveDownloader;
use App\Console\Commands\Emails\ListUploader;
use App\Console\Commands\SymlinkChecker;
use App\Console\Commands\Tasks\Cache\ClearTaskQueueHistory;
use App\Console\Commands\Tasks\Cache\EmailList;
use App\Console\Commands\Tasks\Cache\Result;
use App\Console\Commands\Tasks\Chown;
use App\Console\Commands\Tasks\Daemon;
use App\Console\Commands\Tasks\Execute;
use App\Console\Commands\Tasks\Idler;
use App\Console\Commands\Tasks\Resolve;
use App\Console\Commands\Tester;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClearNewIfExist::class,
        SymlinkChecker::class,
        ClientsNew::class,
        ClientsOld::class,
        ClientsOnline::class,
        Check::class,
        QueueCheck::class,
        ListUploader::class,
        ListDownloader::class,
        EmailList::class,
        ListResolveDownloader::class,
        Execute::class,
        \App\Console\Commands\Clients\Cache\Save::class,
        \App\Console\Commands\Clients\Cache\Delete::class,
        ClearTaskQueueHistory::class,
        Resolve::class,
        Result::class,
        \App\Console\Commands\Tasks\Cache\Resolve::class,
        \App\Console\Commands\Api\ClientNew::class,
        \App\Console\Commands\BlackList\Clear::class,
        Daemon::class,
        Idler::class,
        Chown::class,
        Tester::class,
    ];

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

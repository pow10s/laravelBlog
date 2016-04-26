<?php

namespace App\Console;

use App\ConfirmUsers;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            ConfirmUsers::where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 hours')))->delete();
            User::where('updated_at', '<', date('Y-m-d H:i:s', strtotime('-1 hours')))->where('status', '=',
                0)->delete();
        })->everyMinute();
    }
}

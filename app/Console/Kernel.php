<?php

namespace App\Console;

use App\Jobs\CleanRequestStats;
use App\Jobs\SyncDispatcher;
use App\Jobs\SyncMinecraftUuids;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('horizon:snapshot')->everyFiveMinutes();
        $schedule->command('telescope:prune')->daily();

        $schedule->job(new SyncDispatcher('1day'))->daily();
        $schedule->job(new SyncDispatcher('2day'))->daily()->days([2, 5]);
        $schedule->job(new SyncDispatcher('7day'))->weekly();
        $schedule->job(new SyncMinecraftUuids())->weekly();
        $schedule->job(new CleanRequestStats())->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

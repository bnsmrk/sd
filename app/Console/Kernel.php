<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('notify:due-items')->dailyAt('07:00');
        //   $schedule->command('notify:due-items')->everyMinute(); //every minute will run 
          $schedule->command('notify:due-items')->cron('0 7 */2 * *'); //will run onve every two days.
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

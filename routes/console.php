<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SendDueActivityNotifications;
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule::command('notify:due-items')->everyMinute();
// Schedule::command('notify:due-items')->dailyAt('07:00');


Schedule::command('notify:due-items')->cron('0 7 */2 * *'); //will run once every two days

// Schedule::call(function () {
//     Log::info('⏰ Scheduler ran at ' . now());
// })->everyMinute();

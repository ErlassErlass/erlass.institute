<?php

use Illuminate\Foundation\Console\ClosureCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Console\Commands\SendScheduleReminders;
use Illuminate\Console\Scheduling\Schedule;

Artisan::command('inspire', function () {
    /** @var ClosureCommand $this */
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// The schedule reminder command is handled via App\Console\Commands\SendScheduleReminders

// Schedule the command within the closure for newer Laravel versions or just register it
// Note: In modern Laravel 11, console.php handles scheduling differently.
// For Laravel 10/Earlier:
if (app()->runningInConsole()) {
     app()->booted(function () {
         $schedule = app(Schedule::class);
         $schedule->command('schedule:remind')->everyMinute();
     });
}

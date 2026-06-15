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

         // H-1 WhatsApp reminder: kirim setiap hari jam 18:00 WIB (11:00 UTC)
         $schedule->command('schedule:send-reminders')
                  ->dailyAt('11:00')
                  ->timezone('Asia/Jakarta')
                  ->withoutOverlapping()
                  ->appendOutputTo(storage_path('logs/schedule-reminders.log'));

         // Warning Engine: deteksi otomatis setiap hari jam 21:00 WIB (14:00 UTC)
         $schedule->command('warnings:detect')
                  ->dailyAt('14:00')
                  ->timezone('Asia/Jakarta')
                  ->withoutOverlapping()
                  ->appendOutputTo(storage_path('logs/warnings-detect.log'));
     });
}


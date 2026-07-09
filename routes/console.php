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

         // Purge Temp Exports: bersihkan file ZIP ekspor yang berumur > 30 menit
         $schedule->call(function () {
             $dir = \Illuminate\Support\Facades\Storage::disk('local')->path('temp-exports');
             if (is_dir($dir)) {
                 $files = glob($dir . '/*.zip');
                 foreach ($files as $file) {
                     if (filemtime($file) < now()->subMinutes(30)->timestamp) {
                         @unlink($file);
                     }
                 }
             }
         })->everyFiveMinutes()->name('purge-temp-exports')->withoutOverlapping();
     });
}

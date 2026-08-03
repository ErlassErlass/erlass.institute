<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EkstrakurikulerSession;
use App\Notifications\ScheduleReminderNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendScheduleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:remind';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders to instructors 1 hour before their scheduled session';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for upcoming sessions...');

        // Find sessions starting within the next hour (e.g., between 55 and 65 minutes from now)
        // Adjust the window as needed to avoid double sending or missing sessions due to cron delay
        $startTime = Carbon::now()->addMinutes(55);
        $endTime = Carbon::now()->addMinutes(65);

        $sessions = EkstrakurikulerSession::with(['instruktur', 'rombel.ekstrakurikuler.sekolah'])
            ->where('status', EkstrakurikulerSession::STATUS_TERJADWAL)
            ->whereNull('reminder_h0_sent_at')
            ->whereDate('tanggal_terjadwal', Carbon::today())
            ->whereTime('jam_mulai_terjadwal', '>=', $startTime->format('H:i'))
            ->whereTime('jam_mulai_terjadwal', '<=', $endTime->format('H:i'))
            ->get();

        $count = 0;

        foreach ($sessions as $session) {
            $instructor = $session->instruktur;

            if ($instructor) {
                try {
                    $instructor->notify(new ScheduleReminderNotification($session));
                    $session->update(['reminder_h0_sent_at' => now()]);
                    $this->info("Reminder sent to {$instructor->nama_lengkap} for session ID {$session->id}");
                    Log::info("Schedule Reminder H-0: Sent to {$instructor->nama_lengkap} (ID: {$instructor->id}) for Session ID {$session->id}");
                    $count++;
                } catch (\Exception $e) {
                    $this->error("Failed to send reminder for session {$session->id}: " . $e->getMessage());
                    Log::error("Schedule Reminder Error: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sent {$count} reminders.");
    }
}

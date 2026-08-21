<?php

namespace App\Console\Commands;

use App\Models\EkstrakurikulerSession;
use App\Models\User;
use App\Notifications\UnreportedScheduleReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendUnreportedScheduleReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:send-unreported-reminders 
                            {--dry-run : Menampilkan pratinjau sesi & pesan tanpa mengirim WhatsApp}
                            {--instructor= : ID Instruktur tertentu untuk pengujian spesifik}
                            {--limit= : Batas maksimal sesi yang diproses}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim reminder WhatsApp otomatis jam 18:00 ke instruktur untuk sesi-sesi yang belum dibuat laporannya (maks 3x)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = (bool) $this->option('dry-run');
        $instructorId = $this->option('instructor');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;

        $this->info('===============================================================');
        $this->info('  PENGIRIMAN PENGINGAT OTOMATIS SESI BELUM LAPOR (JAM 18:00)   ');
        $this->info('===============================================================');

        if ($isDryRun) {
            $this->warn('MODE DRY-RUN: Pesan hanya ditampilkan di terminal, tidak dikirim ke WhatsApp.');
        }

        $today = now()->toDateString();
        $currentTime = now()->format('H:i:s');

        // Query sesi yang belum ada laporan mengajar dan memenuhi syarat
        $query = EkstrakurikulerSession::with([
            'ekstrakurikuler.sekolah',
            'rombel',
            'instruktur',
        ])
            ->doesntHave('laporanMengajar')
            ->where('status', '!=', 'dibatalkan')
            ->whereNotNull('user_id_instruktur')
            ->where(function ($q) use ($today, $currentTime) {
                // Sesi hari-hari sebelumnya
                $q->whereDate('tanggal_terjadwal', '<', $today)
                  // ATAU sesi hari ini yang jam selesainya sudah lewat
                  ->orWhere(function ($subQ) use ($today, $currentTime) {
                      $subQ->whereDate('tanggal_terjadwal', $today)
                           ->where(function ($timeQ) use ($currentTime) {
                               $timeQ->whereNull('jam_selesai_terjadwal')
                                     ->orWhere('jam_selesai_terjadwal', '<=', $currentTime);
                           });
                  });
            })
            // Batasan: maksimal 3x diingatkan
            ->where('unreported_reminder_count', '<', 3)
            // Belum pernah dikirim pada tanggal hari yang sama
            ->where(function ($q) use ($today) {
                $q->whereNull('unreported_reminder_last_sent_at')
                  ->orWhereDate('unreported_reminder_last_sent_at', '<', $today);
            })
            // Hanya ekskul yang tidak dibatalkan
            ->whereHas('ekstrakurikuler', function ($q) {
                $q->where('status', '!=', 'dibatalkan');
            })
            ->orderBy('tanggal_terjadwal', 'asc')
            ->orderBy('jam_mulai_terjadwal', 'asc');

        if ($instructorId) {
            $query->where('user_id_instruktur', $instructorId);
        }

        if ($limit) {
            $query->limit($limit);
        }

        $sessions = $query->get();

        if ($sessions->isEmpty()) {
            $this->info('✅ Tidak ada sesi yang perlu diingatkan saat ini.');
            return Command::SUCCESS;
        }

        // Kelompokkan (grouping) per instruktur
        $groupedByInstructor = $sessions->groupBy('user_id_instruktur');

        $this->info("Ditemukan {$sessions->count()} sesi belum lapor dari {$groupedByInstructor->count()} instruktur.");
        $this->line('');

        $sentInstructors = 0;
        $failedInstructors = 0;
        $totalSessionsReminded = 0;

        foreach ($groupedByInstructor as $userId => $instructorSessions) {
            /** @var User|null $instructor */
            $instructor = $instructorSessions->first()->instruktur;

            if (! $instructor) {
                $this->error("  ✗ User ID {$userId} tidak ditemukan di database.");
                $failedInstructors++;
                continue;
            }

            $phone = $instructor->no_wa ?? $instructor->phone_number ?? $instructor->no_telephone ?? null;
            $nama = $instructor->nama_lengkap ?? $instructor->name ?? 'Instruktur';
            $sessionCount = $instructorSessions->count();

            if (! $phone) {
                $this->warn("  ⚠️ [LEWATI] {$nama} (ID: {$userId}) tidak memiliki nomor telepon/WhatsApp.");
                $failedInstructors++;
                continue;
            }

            $notification = new UnreportedScheduleReminderNotification($instructorSessions);
            $messagePreview = $notification->toWhatsApp($instructor);

            if ($isDryRun) {
                $this->line("---------------------------------------------------------------");
                $this->info("📱 [PREVIEW] Instruktur: {$nama} ({$phone}) — {$sessionCount} Sesi");
                $this->line($messagePreview);
                $this->line("---------------------------------------------------------------");
                $sentInstructors++;
                $totalSessionsReminded += $sessionCount;
                continue;
            }

            try {
                // Kirim notifikasi via WhatsApp
                $instructor->notify($notification);

                // Update counter & timestamp untuk setiap sesi yang diingatkan
                foreach ($instructorSessions as $session) {
                    $session->increment('unreported_reminder_count', 1, [
                        'unreported_reminder_last_sent_at' => now(),
                    ]);
                }

                $this->line("  ✓ Terkirim ke: {$nama} ({$phone}) — {$sessionCount} Sesi diingatkan.");
                $sentInstructors++;
                $totalSessionsReminded += $sessionCount;

                Log::info("WhatsApp Unreported Reminder terkirim ke {$nama} ({$phone}) untuk {$sessionCount} sesi.");

                // Catat ke ActivityLog agar Webmaster dapat memantau di /activity-logs
                $webmasterUserId = \App\Models\User::where('role', 'webmaster')->value('id') ?? $instructor->id;
                \App\Models\ActivityLog::create([
                    'user_id' => $webmasterUserId,
                    'action' => 'reminder_wa_1800',
                    'description' => "Pengingat WhatsApp jam 18:00 WIB otomatis terkirim ke instruktur {$nama} ({$phone}) untuk {$sessionCount} sesi belum lapor.",
                    'subject_type' => \App\Models\User::class,
                    'subject_id' => $instructor->id,
                    'properties' => [
                        'instructor_id' => $instructor->id,
                        'instructor_name' => $nama,
                        'session_ids' => $instructorSessions->pluck('id')->toArray(),
                        'total_sessions' => $sessionCount,
                        'phone' => $phone,
                    ],
                    'ip_address' => '127.0.0.1 (Scheduler)',
                    'user_agent' => 'System Cron / schedule:send-unreported-reminders',
                ]);

            } catch (\Exception $e) {
                $this->error("  ✗ Gagal kirim ke {$nama}: " . $e->getMessage());
                Log::error("Gagal kirim WhatsApp Unreported Reminder ke {$nama}: " . $e->getMessage());
                $failedInstructors++;
            }
        }

        $this->line('');
        $this->info("===============================================================");
        $this->info("  RINGKASAN: {$sentInstructors} Instruktur ({$totalSessionsReminded} Sesi) Berhasil, {$failedInstructors} Gagal/Dilewati.");
        $this->info("===============================================================");

        return Command::SUCCESS;
    }
}

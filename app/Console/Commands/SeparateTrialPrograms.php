<?php

namespace App\Console\Commands;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Holiday;
use App\Models\LaporanMengajar;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeparateTrialPrograms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:separate-trial-programs 
                            {--execute : Really execute database changes (default is dry-run)}
                            {--id= : Only process a specific ekstrakurikuler ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Separate Trial/Sosialisasi sessions from regular programs into dedicated Free Trial Class programs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isExecute = (bool) $this->option('execute');
        $filterId = $this->option('id');

        $this->info($isExecute ? '=== EXECUTING MIGRATION ===' : '=== DRY-RUN MODE (No changes made) ===');

        // Find all regular programs that have trial/sosialisasi sessions with meeting number > 0
        if ($filterId) {
            $ids = [$filterId];
        } else {
            $ids = DB::table('laporan_mengajar as l')
                ->join('ekstrakurikuler_session as ses', 'l.ekstrakurikuler_session_id', '=', 'ses.id')
                ->join('ekstrakurikuler as e', 'ses.ekstrakurikuler_id', '=', 'e.id')
                ->whereIn('l.kategori_pengajaran', ['Free Trial Class', 'Sosialisasi bersama Sales'])
                ->whereNotIn('e.kategori_program', ['Free Trial Class', 'Sosialisasi bersama Sales'])
                ->where('ses.nomor_pertemuan', '>', 0)
                ->distinct()
                ->pluck('ses.ekstrakurikuler_id')
                ->toArray();
        }

        $programs = Ekstrakurikuler::whereIn('id', $ids)->with(['sekolah', 'rombels'])->orderBy('id')->get();

        $this->info("Found {$programs->count()} program(s) with trial/sosialisasi sessions to process.");
        $this->newLine();

        $processedCount = 0;

        foreach ($programs as $program) {
            $this->processProgram($program, $isExecute);
            $processedCount++;
        }

        $this->newLine();
        $this->info("Done! Processed {$processedCount} program(s).");
        if (!$isExecute) {
            $this->warn('This was a dry-run. Run with --execute to commit changes.');
        }

        return Command::SUCCESS;
    }

    protected function processProgram(Ekstrakurikuler $program, bool $isExecute): void
    {
        $this->line("--------------------------------------------------");
        $this->info("Program #{$program->id} - {$program->kategori_program}");
        $this->line("Sekolah: {$program->sekolah?->namasekolah} (kodlan: {$program->sekolah_kodlan})");
        $this->line("Target Master: {$program->total_pertemuan} pertemuan, Rombels: {$program->rombels->count()}");

        foreach ($program->rombels as $rombel) {
            // Find trial sessions in this rombel
            $trialSessions = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombel->id)
                ->whereHas('laporanMengajar', function ($q) {
                    $q->whereIn('kategori_pengajaran', ['Free Trial Class', 'Sosialisasi bersama Sales']);
                })
                ->orderBy('tanggal_terjadwal')
                ->orderBy('id')
                ->get();

            if ($trialSessions->isEmpty()) {
                continue;
            }

            $this->comment("  Rombel #{$rombel->id} ({$rombel->nama_rombel}) - Found {$trialSessions->count()} trial/sosialisasi session(s):");
            foreach ($trialSessions as $ts) {
                $lap = $ts->laporanMengajar;
                $this->line("    * Sesi #{$ts->id}: P.{$ts->nomor_pertemuan} ({$ts->tanggal_terjadwal?->format('Y-m-d')}) - Laporan #{$lap?->id} [{$lap?->kategori_pengajaran}]");
            }

            if (!$isExecute) {
                $this->info("    [DRY-RUN] Will create Free Trial Class program, move {$trialSessions->count()} session(s), renumber regular sessions to 1..N, and append missing sessions.");
                continue;
            }

            // Real execution in a transaction per rombel/program
            DB::transaction(function () use ($program, $rombel, $trialSessions) {
                // 1. Create or find Free Trial Class program for this school
                $firstTrial = $trialSessions->first();
                $minDate = $trialSessions->min('tanggal_terjadwal')?->format('Y-m-d') ?? now()->toDateString();
                $maxDate = $trialSessions->max('tanggal_terjadwal')?->format('Y-m-d') ?? now()->toDateString();

                $trialProgram = Ekstrakurikuler::create([
                    'kategori_program' => 'Free Trial Class',
                    'sekolah_kodlan'   => $program->sekolah_kodlan,
                    'jarak_km'         => $program->jarak_km,
                    'total_siswa'      => $rombel->jumlah_siswa ?: 15,
                    'total_ruangan'    => 1,
                    'total_rombel'     => 1,
                    'tanggal_mulai'    => $minDate,
                    'tanggal_selesai'  => $maxDate,
                    'total_pertemuan'  => $trialSessions->count(),
                    'frekuensi'        => 'harian',
                    'status'           => 'selesai',
                    'created_by'       => $program->created_by ?: 1,
                    'updated_by'       => $program->created_by ?: 1,
                    'created_at'       => $minDate . ' 08:00:00',
                    'updated_at'       => now(),
                ]);

                // 2. Create rombel in trial program
                $trialRombel = EkstrakurikulerRombel::create([
                    'ekstrakurikuler_id' => $trialProgram->id,
                    'nama_rombel'        => 'Rombel 1',
                    'nomor_rombel'       => 1,
                    'jumlah_siswa'       => $rombel->jumlah_siswa ?: 15,
                    'tanggal_mulai'      => $minDate,
                    'tanggal_selesai'    => $maxDate,
                    'hari'               => $rombel->hari ?: 'senin',
                    'jam_mulai'          => $firstTrial->jam_mulai_terjadwal ?: $rombel->jam_mulai ?: '08:00:00',
                    'jam_selesai'        => $firstTrial->jam_selesai_terjadwal ?: $rombel->jam_selesai ?: '09:30:00',
                    'total_pertemuan'    => $trialSessions->count(),
                    'frekuensi'          => 'harian',
                    'pertemuan_selesai'  => $trialSessions->count(),
                    'user_id_instruktur' => $firstTrial->user_id_instruktur ?: $rombel->user_id_instruktur,
                    'status'             => 'selesai',
                    'created_by'         => $program->created_by ?: 1,
                    'updated_by'         => $program->created_by ?: 1,
                    'created_at'         => $minDate . ' 08:00:00',
                    'updated_at'         => now(),
                ]);

                // 3. Move trial sessions to new trial program & rombel
                $trialIndex = 1;
                foreach ($trialSessions as $ts) {
                    $ts->update([
                        'ekstrakurikuler_id'        => $trialProgram->id,
                        'ekstrakurikuler_rombel_id' => $trialRombel->id,
                        'nomor_pertemuan'           => $trialIndex,
                    ]);

                    if ($ts->laporanMengajar) {
                        $ts->laporanMengajar->update([
                            'pertemuan_ke' => $trialIndex,
                        ]);
                    }
                    $trialIndex++;
                }

                // 4. Renumber remaining regular sessions in original rombel sequentially 1..N
                $regularSessions = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombel->id)
                    ->orderBy('tanggal_terjadwal')
                    ->orderBy('jam_mulai_terjadwal')
                    ->orderBy('id')
                    ->get();

                // Step 4a: Assign temporary high numbers to prevent UNIQUE constraint violation
                foreach ($regularSessions as $regSession) {
                    DB::table('ekstrakurikuler_session')
                        ->where('id', $regSession->id)
                        ->update(['nomor_pertemuan' => 100000 + $regSession->id]);
                }

                // Step 4b: Assign sequential numbers 1..N
                $seqNumber = 1;
                foreach ($regularSessions as $regSession) {
                    DB::table('ekstrakurikuler_session')
                        ->where('id', $regSession->id)
                        ->update(['nomor_pertemuan' => $seqNumber]);

                    // Update corresponding laporanMengajar
                    if ($regSession->laporanMengajar) {
                        $regSession->laporanMengajar->update(['pertemuan_ke' => $seqNumber]);
                    }

                    $seqNumber++;
                }

                // 5. Append missing sessions if regular session count is less than target
                // Target per rombel: max(32, floor(total_pertemuan / total_rombel))
                $targetPerRombel = max(32, intval($program->total_pertemuan / max(1, $program->total_rombel ?: 1)));
                $currentRegularCount = $regularSessions->count();
                $needed = $targetPerRombel - $currentRegularCount;

                if ($needed > 0 && $regularSessions->isNotEmpty()) {
                    $lastSession = $regularSessions->last();
                    $lastDate = Carbon::parse($lastSession->tanggal_terjadwal);

                    for ($i = 1; $i <= $needed; $i++) {
                        // Find next weekly date (skip holidays)
                        $nextDate = $lastDate->copy()->addWeeks(1);
                        while (Holiday::isHoliday($nextDate)) {
                            $nextDate->addWeeks(1);
                        }
                        $lastDate = $nextDate;

                        $newNumber = $currentRegularCount + $i;

                        EkstrakurikulerSession::create([
                            'ekstrakurikuler_id'        => $program->id,
                            'ekstrakurikuler_rombel_id' => $rombel->id,
                            'nomor_pertemuan'           => $newNumber,
                            'tanggal_terjadwal'         => $nextDate->toDateString(),
                            'jam_mulai_terjadwal'       => $lastSession->jam_mulai_terjadwal ?: $rombel->jam_mulai ?: '08:00:00',
                            'jam_selesai_terjadwal'     => $lastSession->jam_selesai_terjadwal ?: $rombel->jam_selesai ?: '09:30:00',
                            'status'                    => 'terjadwal',
                            'user_id_instruktur'        => $lastSession->user_id_instruktur ?: $rombel->user_id_instruktur,
                            'created_by'                => $program->created_by ?: 1,
                            'updated_by'                => $program->created_by ?: 1,
                            'created_at'                => now(),
                            'updated_at'                => now(),
                        ]);
                    }

                    // Update tanggal_selesai on rombel and program
                    $rombel->update(['tanggal_selesai' => $lastDate->toDateString()]);
                    if (!$program->tanggal_selesai || $lastDate->gt(Carbon::parse($program->tanggal_selesai))) {
                        $program->update(['tanggal_selesai' => $lastDate->toDateString()]);
                    }

                    $this->info("    [SUCCESS] Added {$needed} appended session(s). New end date: {$lastDate->toDateString()}.");
                }

                $this->info("    [SUCCESS] Separated to Free Trial Class #{$trialProgram->id}, Rombel #{$rombel->id} now has {$targetPerRombel} regular sessions.");
            });
        }
    }
}

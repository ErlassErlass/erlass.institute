<?php

namespace App\Jobs;

use App\Exports\AgendaExport;
use App\Models\EkstrakurikulerSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class GenerateAgendaExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 300;
    public int $tries   = 1;

    protected array  $filters;
    protected string $token;

    public function __construct(array $filters, string $token)
    {
        $this->filters = $filters;
        $this->token   = $token;
    }

    public function handle(): void
    {
        try {
            // 1. Query sessions based on filters
            $sessions = $this->querySessions();

            if ($sessions->isEmpty()) {
                Cache::put("agenda_export_{$this->token}", 'error', 1800);
                return;
            }

            // 2. Prepare temp directory
            $tempDir = storage_path("app/temp-exports/{$this->token}");
            @mkdir($tempDir . '/foto',  0755, true);
            @mkdir($tempDir . '/excel', 0755, true);
            @mkdir($tempDir . '/pdf',   0755, true);

            // 3. Build rows array for Excel
            $rows = $sessions->map(function (EkstrakurikulerSession $session) {
                $rombel  = $session->rombel;
                $ekskul  = $rombel?->ekstrakurikuler;
                $sekolah = $ekskul?->sekolah;
                $laporan = $session->laporanMengajar;
                $tanggal = $session->tanggal_pelaksanaan ?? $session->tanggal_terjadwal;

                return [
                    'session_id'          => $session->id,
                    'namsek'              => $sekolah?->namasekolah ?? '—',
                    'kategori_pengajaran' => $ekskul?->kategori_program ?? '—',
                    'rombel'              => $rombel?->nama_rombel ?? '—',
                    'tanggal_mengajar'    => $tanggal ? $tanggal->format('d M Y') : '—',
                    'tanggal_raw'         => $tanggal ? $tanggal->format('Y-m-d') : '00000000',
                    'pertemuan_ke'        => $session->nomor_pertemuan ?? '—',
                    'jumlah_hadir'        => $laporan?->jumlah_siswa_hadir ?? 0,
                    'foto_storage'        => $laporan?->foto_kegiatan,
                    'print_url'           => route('ekstrakurikuler-session.print-session', ['session' => $session->id]),
                ];
            })->toArray();

            // 4. Generate Excel file
            $excelPath = $tempDir . '/excel/Agenda_Kegiatan.xlsx';
            Excel::store(new AgendaExport($rows), "temp-exports/{$this->token}/excel/Agenda_Kegiatan.xlsx");

            // 5. Copy & rename PNG photos
            foreach ($rows as $row) {
                if (empty($row['foto_storage'])) continue;

                $sourcePath = Storage::disk('public')->path($row['foto_storage']);
                if (!file_exists($sourcePath)) continue;

                $namsek    = preg_replace('/[^a-zA-Z0-9]/', '_', substr($row['namsek'], 0, 30));
                $rombel    = preg_replace('/[^a-zA-Z0-9]/', '_', substr($row['rombel'], 0, 20));
                $tanggal   = str_replace('-', '', $row['tanggal_raw']);
                $pertemuan = is_numeric($row['pertemuan_ke']) ? str_pad($row['pertemuan_ke'], 2, '0', STR_PAD_LEFT) : $row['pertemuan_ke'];
                $ext       = pathinfo($sourcePath, PATHINFO_EXTENSION) ?: 'jpg';
                $fileName  = "{$namsek}_{$rombel}_{$tanggal}_Pertemuan{$pertemuan}.{$ext}";

                copy($sourcePath, $tempDir . '/foto/' . $fileName);
            }

            // 6. Generate combined PDF (one multi-page PDF for all sessions)
            $pdfSessions = $sessions->filter(fn($s) => $s->rombel !== null);
            if ($pdfSessions->isNotEmpty()) {
                $pdf = Pdf::loadView('agenda-kegiatan.export-pdf', [
                    'sessions' => $pdfSessions,
                ])->setPaper('a4', 'landscape');
                $pdf->save($tempDir . '/pdf/Agenda_Kegiatan_Presensi.pdf');
            }

            // 7. Build ZIP
            $zipPath = storage_path("app/temp-exports/{$this->token}.zip");
            $zip = new ZipArchive();
            $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $this->addDirectoryToZip($zip, $tempDir . '/excel', 'excel');
            $this->addDirectoryToZip($zip, $tempDir . '/foto', 'foto');
            $this->addDirectoryToZip($zip, $tempDir . '/pdf', 'pdf');

            $zip->close();

            // 8. Clean temp directory
            $this->deleteDirectory($tempDir);

            // 9. Mark as done
            Cache::put("agenda_export_{$this->token}", 'done', 1800);

        } catch (\Throwable $e) {
            Log::error('GenerateAgendaExportJob failed', [
                'token' => $this->token,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            Cache::put("agenda_export_{$this->token}", 'error', 1800);
        }
    }

    protected function querySessions()
    {
        $query = EkstrakurikulerSession::query()
            ->with(['rombel.ekstrakurikuler.sekolah', 'laporanMengajar'])
            ->where('status', 'selesai');

        if (!empty($this->filters['kota'])) {
            $query->whereHas('rombel.ekstrakurikuler.sekolah', function ($q) {
                $q->where('kota', $this->filters['kota']);
            });
        }

        if (!empty($this->filters['sekolah_kodlan'])) {
            $query->whereHas('rombel.ekstrakurikuler.sekolah', function ($q) {
                $q->where('kodlan', $this->filters['sekolah_kodlan']);
            });
        }

        if (!empty($this->filters['rombel_id'])) {
            $query->where('ekstrakurikuler_rombel_id', $this->filters['rombel_id']);
        }

        if (!empty($this->filters['tanggal_dari'])) {
            $query->whereDate('tanggal_pelaksanaan', '>=', $this->filters['tanggal_dari']);
        }

        if (!empty($this->filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_pelaksanaan', '<=', $this->filters['tanggal_sampai']);
        }

        return $query->orderByDesc('tanggal_pelaksanaan')->get();
    }

    protected function addDirectoryToZip(ZipArchive $zip, string $dirPath, string $zipPrefix): void
    {
        if (!is_dir($dirPath)) return;

        $files = glob($dirPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                $zip->addFile($file, $zipPrefix . '/' . basename($file));
            }
        }
    }

    protected function deleteDirectory(string $dir): void
    {
        if (!is_dir($dir)) return;
        $files = array_diff(scandir($dir), ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
}

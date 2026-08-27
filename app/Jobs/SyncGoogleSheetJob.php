<?php

namespace App\Jobs;

use App\Models\LaporanMengajar;
use App\Services\Google\GoogleSheetsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGoogleSheetJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $action;
    public ?int $targetId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $action = 'laporan', ?int $targetId = null)
    {
        $this->action = $action;
        $this->targetId = $targetId;
    }

    /**
     * Execute the job.
     */
    public function handle(GoogleSheetsService $sheetsService): void
    {
        try {
            if ($this->action === 'laporan' && $this->targetId) {
                $laporan = LaporanMengajar::with(['session.ekstrakurikuler.sekolah', 'session.rombel', 'instruktur', 'assisten'])
                    ->find($this->targetId);

                if ($laporan) {
                    $sheetsService->appendLaporanRealtime($laporan);
                }
            } elseif ($this->action === 'full_sync') {
                $sheetsService->syncAllData();
            }
        } catch (\Throwable $e) {
            Log::warning("SyncGoogleSheetJob error ({$this->action}): " . $e->getMessage());
        }
    }
}

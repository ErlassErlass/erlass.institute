<?php

namespace App\Services\Ekstrakurikuler;

use App\Models\Ekstrakurikuler;
use App\Services\SchedulingService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk menangani workflow dan business logic ekstrakurikuler
 * seperti approval, activation, status changes, dan business rules
 */
class EkstrakurikulerWorkflowService
{
    protected SchedulingService $schedulingService;

    public function __construct(SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    /**
     * Approve ekstrakurikuler dengan validasi business rules
     */
    public function approve(Ekstrakurikuler $ekstrakurikuler, ?string $notes = null): array
    {
        DB::beginTransaction();

        try {
            // Validasi apakah dapat disetujui
            if (!$this->canBeApproved($ekstrakurikuler)) {
                throw new \Exception('Program tidak dapat disetujui saat ini.');
            }

            // Business rules validation
            $validationResult = $this->validateForApproval($ekstrakurikuler);
            if (!$validationResult['valid']) {
                throw new \Exception('Program tidak memenuhi kriteria untuk disetujui: ' . implode(', ', $validationResult['errors']));
            }

            // Update status dan data approval
            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_DISETUJUI,
                'tanggal_disetujui' => now(),
                'disetujui_oleh' => Auth::id(),
                'catatan_approval' => $notes,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Ekstrakurikuler approved', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'approved_by' => Auth::id(),
                'notes' => $notes,
            ]);

            return [
                'success' => true,
                'message' => 'Program ekstrakurikuler berhasil disetujui!',
                'data' => $ekstrakurikuler->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error approving ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Reject ekstrakurikuler dengan alasan
     */
    public function reject(Ekstrakurikuler $ekstrakurikuler, string $reason): array
    {
        DB::beginTransaction();

        try {
            if (!$this->canBeRejected($ekstrakurikuler)) {
                throw new \Exception('Program tidak dapat ditolak saat ini.');
            }

            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_DITOLAK,
                'tanggal_ditolak' => now(),
                'ditolak_oleh' => Auth::id(),
                'alasan_penolakan' => $reason,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Ekstrakurikuler rejected', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'rejected_by' => Auth::id(),
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Program ekstrakurikuler berhasil ditolak.',
                'data' => $ekstrakurikuler->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error rejecting ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Activate ekstrakurikuler
     */
    public function activate(Ekstrakurikuler $ekstrakurikuler): array
    {
        DB::beginTransaction();

        try {
            if (!$this->canBeActivated($ekstrakurikuler)) {
                throw new \Exception('Program tidak dapat diaktifkan saat ini.');
            }

            // Validasi sebelum aktivasi
            $validationResult = $this->validateForActivation($ekstrakurikuler);
            if (!$validationResult['valid']) {
                throw new \Exception('Program tidak memenuhi kriteria untuk diaktifkan: ' . implode(', ', $validationResult['errors']));
            }

            // Update status dan tanggal aktivasi
            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_AKTIF,
                'tanggal_aktivasi' => now(),
                'diaktifkan_oleh' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Update status rombel menjadi aktif
            $ekstrakurikuler->rombels()->update([
                'status' => \App\Models\EkstrakurikulerRombel::STATUS_BERLANGSUNG,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Ekstrakurikuler activated', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'activated_by' => Auth::id(),
            ]);

            return [
                'success' => true,
                'message' => 'Program ekstrakurikuler berhasil diaktifkan!',
                'data' => $ekstrakurikuler->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error activating ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Complete ekstrakurikuler (mark as finished)
     */
    public function complete(Ekstrakurikuler $ekstrakurikuler): array
    {
        DB::beginTransaction();

        try {
            if (!$this->canBeCompleted($ekstrakurikuler)) {
                throw new \Exception('Program tidak dapat diselesaikan saat ini.');
            }

            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_SELESAI,
                'tanggal_selesai_aktual' => now(),
                'diselesaikan_oleh' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);

            // Update status rombel
            $ekstrakurikuler->rombels()->update([
                'status' => \App\Models\EkstrakurikulerRombel::STATUS_SELESAI,
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info('Ekstrakurikuler completed', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'completed_by' => Auth::id(),
            ]);

            return [
                'success' => true,
                'message' => 'Program ekstrakurikuler berhasil diselesaikan!',
                'data' => $ekstrakurikuler->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error completing ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Cancel ekstrakurikuler
     */
    public function cancel(Ekstrakurikuler $ekstrakurikuler, string $reason): array
    {
        DB::beginTransaction();

        try {
            if (!$this->canBeCancelled($ekstrakurikuler)) {
                throw new \Exception('Program tidak dapat dibatalkan saat ini.');
            }

            $ekstrakurikuler->update([
                'status' => Ekstrakurikuler::STATUS_DIBATALKAN,
                'tanggal_dibatalkan' => now(),
                'dibatalkan_oleh' => Auth::id(),
                'alasan_pembatalan' => $reason,
                'updated_by' => Auth::id(),
            ]);

            // Cancel sessions yang belum dimulai
            $ekstrakurikuler->sessions()
                ->where('status', \App\Models\EkstrakurikulerSession::STATUS_TERJADWAL)
                ->update([
                    'status' => \App\Models\EkstrakurikulerSession::STATUS_DIBATALKAN,
                    'updated_by' => Auth::id(),
                ]);

            DB::commit();

            Log::info('Ekstrakurikuler cancelled', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'cancelled_by' => Auth::id(),
                'reason' => $reason,
            ]);

            return [
                'success' => true,
                'message' => 'Program ekstrakurikuler berhasil dibatalkan.',
                'data' => $ekstrakurikuler->fresh(),
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error cancelling ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Regenerate sessions untuk ekstrakurikuler
     */
    public function regenerateSessions(Ekstrakurikuler $ekstrakurikuler, array $options = []): array
    {
        DB::beginTransaction();

        try {
            $totalSessions = 0;
            $rombels = $ekstrakurikuler->rombels;

            foreach ($rombels as $rombel) {
                $sessions = $this->schedulingService->generateSessionsForRombel($rombel, array_merge([
                    'replace_existing' => true,
                    'skip_holidays' => true,
                ], $options));

                $totalSessions += $sessions->count();
            }

            DB::commit();

            Log::info('Sessions regenerated for ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'total_sessions' => $totalSessions,
                'rombel_count' => $rombels->count(),
                'regenerated_by' => Auth::id(),
            ]);

            return [
                'success' => true,
                'message' => "Berhasil regenerate {$totalSessions} sessions untuk {$rombels->count()} rombel",
                'data' => [
                    'total_sessions' => $totalSessions,
                    'rombel_count' => $rombels->count(),
                ],
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Error regenerating sessions for ekstrakurikuler', [
                'ekstrakurikuler_id' => $ekstrakurikuler->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal regenerate sessions: ' . $e->getMessage(),
                'data' => null,
            ];
        }
    }

    /**
     * Cek apakah ekstrakurikuler dapat disetujui
     */
    public function canBeApproved(Ekstrakurikuler $ekstrakurikuler): bool
    {
        return $ekstrakurikuler->status === Ekstrakurikuler::STATUS_DIAJUKAN;
    }

    /**
     * Cek apakah ekstrakurikuler dapat ditolak
     */
    public function canBeRejected(Ekstrakurikuler $ekstrakurikuler): bool
    {
        return in_array($ekstrakurikuler->status, [
            Ekstrakurikuler::STATUS_DIAJUKAN,
            Ekstrakurikuler::STATUS_DISETUJUI,
        ]);
    }

    /**
     * Cek apakah ekstrakurikuler dapat diaktifkan
     */
    public function canBeActivated(Ekstrakurikuler $ekstrakurikuler): bool
    {
        return $ekstrakurikuler->status === Ekstrakurikuler::STATUS_DISETUJUI;
    }

    /**
     * Cek apakah ekstrakurikuler dapat diselesaikan
     */
    public function canBeCompleted(Ekstrakurikuler $ekstrakurikuler): bool
    {
        return in_array($ekstrakurikuler->status, [
            Ekstrakurikuler::STATUS_AKTIF,
        ]);
    }

    /**
     * Cek apakah ekstrakurikuler dapat dibatalkan
     */
    public function canBeCancelled(Ekstrakurikuler $ekstrakurikuler): bool
    {
        return !in_array($ekstrakurikuler->status, [
            Ekstrakurikuler::STATUS_SELESAI,
            Ekstrakurikuler::STATUS_DIBATALKAN,
        ]);
    }

    /**
     * Validasi untuk approval
     */
    protected function validateForApproval(Ekstrakurikuler $ekstrakurikuler): array
    {
        $errors = [];

        // Cek kelengkapan data dasar
        if (empty($ekstrakurikuler->sekolah_kodlan)) {
            $errors[] = 'Data sekolah tidak lengkap';
        }

        if (empty($ekstrakurikuler->user_id_sales)) {
            $errors[] = 'Sales/koordinator belum ditentukan';
        }

        if ($ekstrakurikuler->total_rombel < 1) {
            $errors[] = 'Minimal harus ada 1 rombel';
        }

        // Cek kelengkapan rombel
        $rombelCount = $ekstrakurikuler->rombels()->count();
        if ($rombelCount < $ekstrakurikuler->total_rombel) {
            $errors[] = 'Data rombel tidak lengkap';
        }

        // Cek validasi tanggal (Validation relaxed to allow Start Date <= Today if needed, or strictly future?)
        // Ideally program starts in future. But user might be backdating or starting today.
        // Let's only forbid if it's older than 30 days ago to prevent ancient data approval?
        // Or simply allow "Today".
        // $ekstrakurikuler->tanggal_mulai (00:00:00) < now() (12:00:00) => True (isPast).
        
        // Fix: Allow today.
        if ($ekstrakurikuler->tanggal_mulai && $ekstrakurikuler->tanggal_mulai->endOfDay()->isPast()) {
           // still strictly past? No. 
           // If date is 29th. End of day is 29th 23:59:59. Now is 29th 12:00. isPast() is false.
           // If date is 28th. End of day is 28th 23:59:59. Now is 29th 12:00. isPast() is true.
           // This logic allows Today.
             $errors[] = 'Tanggal mulai tidak boleh lewat dari hari ini (sudah berlalu)';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Validasi untuk aktivasi
     */
    protected function validateForActivation(Ekstrakurikuler $ekstrakurikuler): array
    {
        $errors = [];

        // Cek semua rombel memiliki sessions
        foreach ($ekstrakurikuler->rombels as $rombel) {
            $sessionCount = $rombel->sessions()->count();
            if ($sessionCount === 0) {
                $errors[] = "Rombel {$rombel->nama_rombel} belum memiliki jadwal sessions";
            }
        }

        // Cek instruktur sudah assigned (opsional, tergantung business rule)
        // if (!$ekstrakurikuler->user_id_instruktur) {
        //     $errors[] = 'Instruktur belum ditentukan';
        // }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Get available status transitions untuk ekstrakurikuler
     */
    public function getAvailableTransitions(Ekstrakurikuler $ekstrakurikuler): array
    {
        $transitions = [];
        $currentStatus = $ekstrakurikuler->status;
        $userRole = Auth::user()->role;

        // Admin/webmaster dapat melakukan semua transisi
        $isAdmin = in_array($userRole, ['admin', 'webmaster']);

        switch ($currentStatus) {
            case Ekstrakurikuler::STATUS_DRAFT:
                $transitions[] = ['action' => 'submit', 'label' => 'Ajukan', 'target' => Ekstrakurikuler::STATUS_DIAJUKAN];
                if ($isAdmin) {
                    $transitions[] = ['action' => 'approve', 'label' => 'Setujui Langsung', 'target' => Ekstrakurikuler::STATUS_DISETUJUI];
                }
                break;

            case Ekstrakurikuler::STATUS_DIAJUKAN:
                if ($isAdmin) {
                    $transitions[] = ['action' => 'approve', 'label' => 'Setujui', 'target' => Ekstrakurikuler::STATUS_DISETUJUI];
                    $transitions[] = ['action' => 'reject', 'label' => 'Tolak', 'target' => Ekstrakurikuler::STATUS_DITOLAK];
                }
                break;

            case Ekstrakurikuler::STATUS_DISETUJUI:
                if ($isAdmin) {
                    $transitions[] = ['action' => 'activate', 'label' => 'Aktifkan', 'target' => Ekstrakurikuler::STATUS_AKTIF];
                    $transitions[] = ['action' => 'reject', 'label' => 'Tolak', 'target' => Ekstrakurikuler::STATUS_DITOLAK];
                }
                break;

            case Ekstrakurikuler::STATUS_AKTIF:
                if ($isAdmin) {
                    $transitions[] = ['action' => 'complete', 'label' => 'Selesaikan', 'target' => Ekstrakurikuler::STATUS_SELESAI];
                }
                break;

            case Ekstrakurikuler::STATUS_DITOLAK:
                $transitions[] = ['action' => 'submit', 'label' => 'Ajukan Ulang', 'target' => Ekstrakurikuler::STATUS_DIAJUKAN];
                break;
        }

        // Cancel action tersedia untuk hampir semua status (kecuali selesai/cancelled)
        if (!in_array($currentStatus, [Ekstrakurikuler::STATUS_SELESAI, Ekstrakurikuler::STATUS_DIBATALKAN])) {
            $transitions[] = ['action' => 'cancel', 'label' => 'Batalkan', 'target' => Ekstrakurikuler::STATUS_DIBATALKAN];
        }

        return $transitions;
    }
}
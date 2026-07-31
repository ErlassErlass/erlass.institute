<?php

namespace App\Observers;

use App\Models\Absensi;
use App\Models\ActivityLog;
use App\Models\Siswa;
use Illuminate\Support\Facades\Auth;

class AbsensiObserver
{
    /**
     * Handle the Absensi "created" event.
     */
    public function created(Absensi $absensi): void
    {
        $this->logActivity('absensi_created', $absensi, null, $absensi->status);
    }

    /**
     * Handle the Absensi "updated" event.
     */
    public function updated(Absensi $absensi): void
    {
        if ($absensi->isDirty('status')) {
            $oldStatus = $absensi->getOriginal('status');
            $newStatus = $absensi->status;
            $this->logActivity('absensi_updated', $absensi, $oldStatus, $newStatus);
        }
    }

    /**
     * Handle the Absensi "deleted" event.
     */
    public function deleted(Absensi $absensi): void
    {
        $this->logActivity('absensi_deleted', $absensi, $absensi->status, null);
    }

    /**
     * Internal helper to record activity log.
     */
    protected function logActivity(string $action, Absensi $absensi, ?string $oldStatus, ?string $newStatus): void
    {
        try {
            $user = Auth::user();
            $userName = $user ? $user->nama_lengkap . " ({$user->role})" : 'System';
            $siswa = Siswa::find($absensi->siswa_id);
            $namaSiswa = $siswa ? $siswa->nama_lengkap : "Siswa #{$absensi->siswa_id}";

            $desc = match($action) {
                'absensi_created' => "{$userName} mencatat absensi siswa {$namaSiswa} [{$newStatus}] pada Laporan #{$absensi->laporan_mengajar_id}.",
                'absensi_updated' => "{$userName} MENGUBAH status absensi siswa {$namaSiswa} dari [{$oldStatus}] menjadi [{$newStatus}] pada Laporan #{$absensi->laporan_mengajar_id}.",
                'absensi_deleted' => "{$userName} MENGHAPUS data absensi siswa {$namaSiswa} (status sebelumnya: [{$oldStatus}]) pada Laporan #{$absensi->laporan_mengajar_id}.",
                default => "{$userName} mengubah absensi siswa {$namaSiswa}."
            };

            ActivityLog::create([
                'user_id' => $user ? $user->id : null,
                'action' => $action,
                'description' => $desc,
                'subject_type' => Absensi::class,
                'subject_id' => $absensi->id,
                'properties' => [
                    'siswa_id' => $absensi->siswa_id,
                    'nama_siswa' => $namaSiswa,
                    'laporan_mengajar_id' => $absensi->laporan_mengajar_id,
                    'status_lama' => $oldStatus,
                    'status_baru' => $newStatus,
                ],
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to log absensi activity: ' . $e->getMessage());
        }
    }
}

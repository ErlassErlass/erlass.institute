<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Notification;
use Carbon\Carbon;

class MilestoneNotificationService
{
    /**
     * Non-teaching / non-conducted statuses that must be excluded from milestone teaching dates.
     */
    const EXCLUDED_STATUSES = [
        EkstrakurikulerSession::STATUS_LIBUR,
        EkstrakurikulerSession::STATUS_DITUNDA,
        EkstrakurikulerSession::STATUS_DIBATALKAN,
        EkstrakurikulerSession::STATUS_TIDAK_HADIR,
    ];

    /**
     * Trigger milestone notification if pertemuan_ke is a multiple of 4 (e.g. 4, 8, 12, 16, 20, 24, 28, 32).
     * Strictly filters out holidays (libur), postponed (ditunda), and non-conducted sessions.
     */
    public function checkAndTriggerMilestoneNotification(EkstrakurikulerSession $session, LaporanMengajar $laporan): ?Notification
    {
        $pertemuanKe = (int) ($laporan->pertemuan_ke ?: $session->nomor_pertemuan);

        if ($pertemuanKe <= 0 || $pertemuanKe % 4 !== 0) {
            return null;
        }

        $rombelId = $session->ekstrakurikuler_rombel_id;

        // Fetch teaching dates for 4 actual completed sessions excluding libur / ditunda / dibatalkan
        $tanggalMengajarList = $this->getTeachingDatesForMilestone($rombelId, $pertemuanKe, $session, $laporan);

        // Syarat mutlak: Harus ada tepat 4 sesi mengajar riil yang selesai
        // Jika ada sesi yang ditunda/libur sehingga baru 1, 2, atau 3 sesi riil yang selesai, milestone BELUM tercapai.
        if (count($tanggalMengajarList) < 4) {
            return null;
        }

        $sekolahNama = $session->rombel?->ekstrakurikuler?->sekolah?->namasekolah 
                    ?? $laporan->sekolah_nama 
                    ?? 'Erlass Institute';
        $kategori = $session->rombel?->ekstrakurikuler?->kategori_program 
                 ?? $laporan->kategori_pengajaran 
                 ?? 'Ekskul';
        $rombelNama = $session->rombel?->nama_rombel ?? $laporan->rombel ?? '-';
        $instrukturNama = $laporan->instruktur?->nama_lengkap 
                        ?? $session->instruktur?->nama_lengkap 
                        ?? 'Instruktur';

        $fotoAbsensiUrl = $laporan->foto_absensi_siswa ? asset('storage/' . $laporan->foto_absensi_siswa) : null;
        $fotoKegiatanUrl = $laporan->foto_kegiatan ? asset('storage/' . $laporan->foto_kegiatan) : null;
        $reportDetailUrl = route('laporan-mengajar.show', $laporan->id);

        $dataPayload = [
            'laporan_id' => $laporan->id,
            'session_id' => $session->id,
            'rombel_id' => $rombelId,
            'sekolah_nama' => $sekolahNama,
            'kategori' => $kategori,
            'rombel' => $rombelNama,
            'instruktur_nama' => $instrukturNama,
            'pertemuan_ke' => $pertemuanKe,
            'jumlah_hadir' => $laporan->jumlah_siswa_hadir,
            'tanggal_mengajar_4' => $tanggalMengajarList,
            'foto_absensi_url' => $fotoAbsensiUrl,
            'foto_kegiatan_url' => $fotoKegiatanUrl,
            'report_detail_url' => $reportDetailUrl,
        ];

        $title = "🔔 Laporan Milestone Pertemuan Ke-{$pertemuanKe} Selesai";
        $message = "{$sekolahNama} — {$kategori} ({$rombelNama}). Instruktur {$instrukturNama} telah menyelesaikan laporan pertemuan ke-{$pertemuanKe}.";

        return Notification::create([
            'type' => 'milestone_report',
            'target_roles' => 'admin,webmaster,admin_sistem',
            'title' => $title,
            'message' => $message,
            'data' => $dataPayload,
            'is_read' => false,
        ]);
    }

    /**
     * Get the 4 valid teaching dates for a milestone block, ignoring libur/ditunda/dibatalkan sessions.
     */
    public function getTeachingDatesForMilestone(?int $rombelId, int $pertemuanKe, ?EkstrakurikulerSession $currentSession = null, ?LaporanMengajar $currentLaporan = null): array
    {
        if (!$rombelId) {
            return [];
        }

        // Fetch sessions for this rombel up to current milestone meeting, strictly excluding libur/ditunda/dibatalkan
        $validSessions = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombelId)
            ->whereNotIn('status', self::EXCLUDED_STATUSES)
            ->where('nomor_pertemuan', '<=', $pertemuanKe)
            ->where(function($q) use ($currentSession) {
                $q->where('status', EkstrakurikulerSession::STATUS_SELESAI)
                  ->orWhereHas('laporanMengajar');
                if ($currentSession) {
                    $q->orWhere('id', $currentSession->id);
                }
            })
            ->with('laporanMengajar')
            ->orderBy('nomor_pertemuan', 'desc')
            ->take(4)
            ->get()
            ->sortBy('nomor_pertemuan')
            ->values();

        $tanggalMengajarList = [];
        foreach ($validSessions as $bSession) {
            $tgl = null;
            if ($bSession->laporanMengajar && $bSession->laporanMengajar->jadwal_mengajar) {
                $tgl = Carbon::parse($bSession->laporanMengajar->jadwal_mengajar)->format('d-m-Y');
            } elseif ($currentLaporan && $bSession->id === $currentSession?->id && $currentLaporan->jadwal_mengajar) {
                $tgl = Carbon::parse($currentLaporan->jadwal_mengajar)->format('d-m-Y');
            } elseif ($bSession->tanggal_pelaksanaan) {
                $tgl = Carbon::parse($bSession->tanggal_pelaksanaan)->format('d-m-Y');
            } elseif ($bSession->tanggal_terjadwal) {
                $tgl = Carbon::parse($bSession->tanggal_terjadwal)->format('d-m-Y');
            }

            $tanggalMengajarList[] = [
                'pertemuan_ke' => $bSession->nomor_pertemuan,
                'tanggal' => $tgl ?: '-',
            ];
        }

        return $tanggalMengajarList;
    }

    /**
     * Recalibrate existing milestone notifications in database to ensure
     * libur / ditunda sessions are excluded, and any notification with < 4 completed sessions is purged.
     */
    public function recalibrateExistingMilestoneNotifications(): int
    {
        $notifications = Notification::where('type', 'milestone_report')->get();
        $processedCount = 0;

        foreach ($notifications as $notif) {
            $data = $notif->data ?? [];
            $sessionId = $data['session_id'] ?? null;
            $pertemuanKe = (int) ($data['pertemuan_ke'] ?? 0);
            $rombelId = $data['rombel_id'] ?? null;

            if (!$rombelId && $sessionId) {
                $session = EkstrakurikulerSession::find($sessionId);
                $rombelId = $session?->ekstrakurikuler_rombel_id;
            }

            if (!$rombelId || $pertemuanKe <= 0) {
                $notif->delete();
                $processedCount++;
                continue;
            }

            $recalibratedDates = $this->getTeachingDatesForMilestone($rombelId, $pertemuanKe);

            // Jika sesi mengajar riil yang selesai kurang dari 4 (misal karena ada yang libur/ditunda),
            // maka milestone tersebut belum lengkap dan harus dihapus dari daftar notifikasi.
            if (count($recalibratedDates) < 4) {
                $notif->delete();
                $processedCount++;
                continue;
            }

            $data['rombel_id'] = $rombelId;
            $data['tanggal_mengajar_4'] = $recalibratedDates;
            $notif->data = $data;
            $notif->save();
            $processedCount++;
        }

        return $processedCount;
    }
}

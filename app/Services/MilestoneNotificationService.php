<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Notification;
use Carbon\Carbon;

class MilestoneNotificationService
{
    /**
     * Trigger milestone notification if pertemuan_ke is a multiple of 4 (e.g. 4, 8, 12, 16, 20, 24, 28, 32).
     */
    public function checkAndTriggerMilestoneNotification(EkstrakurikulerSession $session, LaporanMengajar $laporan): ?Notification
    {
        $pertemuanKe = (int) $laporan->pertemuan_ke;

        if ($pertemuanKe <= 0 || $pertemuanKe % 4 !== 0) {
            return null;
        }

        // Determine block range: for 4 => 1..4, for 8 => 5..8, for 12 => 9..12, etc.
        $startNum = max(1, $pertemuanKe - 3);
        $endNum = $pertemuanKe;

        // Fetch sessions for this rombel in the 4-meeting block
        $blockSessions = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $session->ekstrakurikuler_rombel_id)
            ->whereBetween('nomor_pertemuan', [$startNum, $endNum])
            ->orderBy('nomor_pertemuan', 'asc')
            ->with('laporanMengajar')
            ->get();

        $tanggalMengajarList = [];
        foreach ($blockSessions as $bSession) {
            $tgl = null;
            if ($bSession->laporanMengajar && $bSession->laporanMengajar->jadwal_mengajar) {
                $tgl = Carbon::parse($bSession->laporanMengajar->jadwal_mengajar)->format('d-m-Y');
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
}

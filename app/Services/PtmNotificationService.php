<?php

namespace App\Services;

use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PtmNotificationService
{
    /**
     * Trigger atau perbarui notifikasi PTM selesai untuk Admin saat instruktur
     * menyelesaikan laporan mengajar atau sesi pertemuan ekstrakurikuler tatap muka (PTM).
     */
    public function triggerPtmCompletedNotification(?EkstrakurikulerSession $session, ?LaporanMengajar $laporan): ?Notification
    {
        if (!$session && !$laporan) {
            return null;
        }

        // Cari relasi jika salah satu null
        if (!$session && $laporan && $laporan->ekstrakurikulerSession) {
            $session = $laporan->ekstrakurikulerSession;
        }
        if (!$laporan && $session && $session->laporanMengajar) {
            $laporan = $session->laporanMengajar;
        }

        // Eager load relasi rombel, ekskul, dan sekolah
        $rombel = $session?->rombel;
        $ekskul = $rombel?->ekstrakurikuler;
        $sekolah = $ekskul?->sekolah ?? $laporan?->sekolah;

        $sekolahNama = $sekolah?->namasekolah 
            ?? $laporan?->sekolah_nama 
            ?? 'Erlass Institute';

        $kategori = $ekskul?->kategori_program 
            ?? $laporan?->kategori_pengajaran 
            ?? 'Ekstrakurikuler';

        $rombelNama = $rombel?->nama_rombel 
            ?? $laporan?->rombel 
            ?? '-';

        $pertemuanKe = $session?->nomor_pertemuan 
            ?? $laporan?->pertemuan_ke 
            ?? 1;

        $instrukturNama = $laporan?->instruktur?->nama_lengkap 
            ?? $session?->instruktur?->nama_lengkap 
            ?? ($rombel?->instruktur?->nama_lengkap ?? 'Instruktur Erlass');

        // Tanggal mengajar
        $tglObj = $session?->tanggal_pelaksanaan ?? $session?->tanggal_terjadwal ?? $laporan?->jadwal_mengajar;
        if ($tglObj && is_string($tglObj)) {
            try { $tglObj = Carbon::parse($tglObj); } catch (\Throwable $e) {}
        }
        $tanggalFormatted = ($tglObj instanceof Carbon) ? $tglObj->translatedFormat('d F Y') : ($tglObj ?? date('d F Y'));
        $tanggalShort = ($tglObj instanceof Carbon) ? $tglObj->format('d/m/Y') : '-';

        // Jam pelaksanaan
        $jamMulai = $session?->jam_mulai_aktual ?? $session?->jam_mulai_terjadwal ?? ($laporan?->jam_mulai ?? '-');
        $jamSelesai = $session?->jam_selesai_aktual ?? $session?->jam_selesai_terjadwal ?? ($laporan?->jam_selesai ?? '-');
        $jamFormatted = ($jamMulai && $jamSelesai && $jamMulai !== '-' && $jamSelesai !== '-') ? "{$jamMulai} - {$jamSelesai}" : ($jamMulai ?? '-');

        // Jumlah siswa hadir
        $jumlahHadir = 0;
        if ($laporan) {
            $jumlahHadir = $laporan->jumlah_siswa_hadir ?? $laporan->absensis()->where('status', 'hadir')->count();
        } elseif ($session && $session->laporanMengajar) {
            $jumlahHadir = $session->laporanMengajar->jumlah_siswa_hadir ?? 0;
        }

        $reportDetailUrl = $laporan ? route('laporan-mengajar.show', $laporan->id) : null;
        $fotoAbsensiUrl = ($laporan && $laporan->foto_absensi_siswa) ? asset('storage/' . $laporan->foto_absensi_siswa) : null;
        $fotoKegiatanUrl = ($laporan && $laporan->foto_kegiatan) ? asset('storage/' . $laporan->foto_kegiatan) : null;

        $wilayah = $sekolah?->kota ?? ($sekolah?->kotkab ?? '-');

        $title = "🏫 PTM Selesai: {$sekolahNama} (P.{$pertemuanKe})";
        $message = "Instruktur {$instrukturNama} telah menyelesaikan PTM Pertemuan ke-{$pertemuanKe} untuk rombel {$rombelNama} di {$sekolahNama} ({$wilayah}). Hadir: {$jumlahHadir} siswa.";

        $dataPayload = [
            'type_label'         => 'PTM Selesai',
            'session_id'         => $session?->id,
            'laporan_id'         => $laporan?->id,
            'rombel_id'          => $rombel?->id,
            'sekolah_kodlan'     => $sekolah?->kodlan ?? $laporan?->sekolah_kodlan,
            'sekolah_nama'       => $sekolahNama,
            'wilayah'            => $wilayah,
            'kategori'           => $kategori,
            'rombel'             => $rombelNama,
            'pertemuan_ke'       => (int) $pertemuanKe,
            'instruktur_nama'    => $instrukturNama,
            'tanggal'            => $tanggalFormatted,
            'tanggal_short'      => $tanggalShort,
            'jam'                => $jamFormatted,
            'jumlah_hadir'       => (int) $jumlahHadir,
            'report_detail_url'  => $reportDetailUrl,
            'foto_absensi_url'   => $fotoAbsensiUrl,
            'foto_kegiatan_url'  => $fotoKegiatanUrl,
            'invoice_queue_url'  => route('invoice.index'),
        ];

        // Mencegah duplikasi notifikasi untuk sesi/rombel yang sama
        $query = Notification::where('type', 'ptm_completed');
        if ($session) {
            $query->where('data->session_id', $session->id);
        } elseif ($rombel && $pertemuanKe) {
            $query->where('data->rombel_id', $rombel->id)
                  ->where('data->pertemuan_ke', (int) $pertemuanKe);
        } elseif ($laporan) {
            $query->where('data->laporan_id', $laporan->id);
        }

        $existing = $query->orderBy('id', 'desc')->get();
        if ($existing->isNotEmpty()) {
            $primary = $existing->first();
            $primary->update([
                'title'   => $title,
                'message' => $message,
                'data'    => $dataPayload,
            ]);

            if ($existing->count() > 1) {
                $existing->slice(1)->each->delete();
            }

            return $primary;
        }

        return Notification::create([
            'type'         => 'ptm_completed',
            'target_roles' => 'admin,webmaster,admin_sistem',
            'title'        => $title,
            'message'      => $message,
            'data'         => $dataPayload,
            'is_read'      => false,
        ]);
    }
}

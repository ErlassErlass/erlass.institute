<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Notification;
use App\Models\Sekolah;
use App\Models\User;
use App\Services\MilestoneNotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminMilestoneNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_milestone_notification_triggered_on_meeting_4(): void
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_sistem',
            'status' => 'Aktif',
        ]);

        $instructor = User::create([
            'nama_lengkap' => 'Raditya Instructor',
            'email' => 'raditya@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'verification_status' => 'approved',
        ]);

        $sekolah = Sekolah::create([
            'kodlan' => 'ERL12345',
            'namasekolah' => 'SMPK IGNATIUS SLAMET RIYADI',
            'jenjang' => 'SMP',
            'status' => 'Aktif',
            'kec' => 'Kecamatan Test',
            'kotkab' => 'Kota Jakarta Timur',
            'kota' => 'JAKARTA TIMUR',
            'provinsi' => 'DKI Jakarta',
        ]);

        $ekskul = Ekstrakurikuler::create([
            'nama_program' => 'Coding Scratch',
            'sekolah_id' => $sekolah->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'kategori_program' => 'Coding Scratch',
            'total_siswa' => 15,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'tahun_ajaran' => '2025/2026',
            'status' => 'aktif',
            'created_by' => $instructor->id,
        ]);

        // Automatically generates 12 sessions
        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel' => 1,
            'nama_rombel' => 'Rombel 1',
            'jumlah_siswa' => 15,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $instructor->id,
        ]);

        // Create reports for sessions 1, 2, 3, 4
        for ($i = 1; $i <= 3; $i++) {
            $s = $rombel->sessions()->where('nomor_pertemuan', $i)->first();
            $s->update(['status' => EkstrakurikulerSession::STATUS_SELESAI]);
            LaporanMengajar::create([
                'ekstrakurikuler_session_id' => $s->id,
                'user_id_instruktur' => $instructor->id,
                'pertemuan_ke' => $i,
                'rombel' => $rombel->nama_rombel,
                'sekolah_kodlan' => $sekolah->kodlan,
                'sekolah_nama' => $sekolah->namasekolah,
                'jadwal_mengajar' => "2025-08-0{$i}",
                'jam_mulai' => '13:00',
                'jam_selesai' => '14:30',
                'kategori_pengajaran' => 'Coding Scratch',
                'materi_pengajaran' => "Materi {$i}",
                'jumlah_siswa_hadir' => 12,
                'refleksi_siswa' => '-',
                'refleksi_capaian' => '-',
                'keaktifan' => 'aktif',
                'pemahaman_materi' => 'paham',
            ]);
        }

        $session4 = $rombel->sessions()->where('nomor_pertemuan', 4)->first();
        $this->assertNotNull($session4);
        $session4->update(['status' => EkstrakurikulerSession::STATUS_SELESAI]);

        // Create report for meeting 4
        $laporan = LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session4->id,
            'user_id_instruktur' => $instructor->id,
            'pertemuan_ke' => 4,
            'rombel' => $rombel->nama_rombel,
            'sekolah_kodlan' => $sekolah->kodlan,
            'sekolah_nama' => $sekolah->namasekolah,
            'jadwal_mengajar' => '2025-08-04',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Coding Scratch',
            'materi_pengajaran' => 'Variabel & Loop',
            'jumlah_siswa_hadir' => 12,
            'jumlah_siswa_tidak_hadir' => 1,
            'foto_kegiatan' => 'reports/test.jpg',
            'foto_absensi_siswa' => 'reports/absensi.jpg',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
        ]);

        $service = new MilestoneNotificationService();
        $notif = $service->checkAndTriggerMilestoneNotification($session4, $laporan);

        $this->assertNotNull($notif);
        $this->assertEquals('milestone_report', $notif->type);
        $this->assertFalse($notif->is_read);
        $this->assertEquals(4, $notif->data['pertemuan_ke']);
        $this->assertEquals(12, $notif->data['jumlah_hadir']);
        $this->assertCount(4, $notif->data['tanggal_mengajar_4']);
    }

    public function test_non_milestone_meeting_does_not_trigger_notification(): void
    {
        $instructor = User::create([
            'nama_lengkap' => 'Raditya Instructor',
            'email' => 'raditya2@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'verification_status' => 'approved',
        ]);

        $sekolah = Sekolah::create([
            'kodlan' => 'ERL54321',
            'namasekolah' => 'SD Bintang Kejora',
            'jenjang' => 'SD',
            'status' => 'aktif',
            'kec' => 'Kecamatan Test',
            'kotkab' => 'Kota Jakarta Timur',
            'kota' => 'JAKARTA TIMUR',
            'provinsi' => 'DKI Jakarta',
        ]);

        $ekskul = Ekstrakurikuler::create([
            'nama_program' => 'Coding Scratch',
            'sekolah_id' => $sekolah->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'kategori_program' => 'Coding Scratch',
            'total_siswa' => 15,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'tahun_ajaran' => '2025/2026',
            'status' => 'aktif',
            'created_by' => $instructor->id,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel' => 1,
            'nama_rombel' => 'Rombel 1',
            'jumlah_siswa' => 15,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $instructor->id,
        ]);

        $session3 = $rombel->sessions()->where('nomor_pertemuan', 3)->first();
        $this->assertNotNull($session3);

        $laporan = LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session3->id,
            'user_id_instruktur' => $instructor->id,
            'pertemuan_ke' => 3,
            'rombel' => $rombel->nama_rombel,
            'sekolah_kodlan' => $sekolah->kodlan,
            'jadwal_mengajar' => '2025-08-03',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Coding Scratch',
            'materi_pengajaran' => 'Pertemuan 3',
            'jumlah_siswa_hadir' => 12,
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
        ]);

        $service = new MilestoneNotificationService();
        $notif = $service->checkAndTriggerMilestoneNotification($session3, $laporan);

        $this->assertNull($notif);
    }

    public function test_milestone_notification_excludes_libur_and_ditunda_sessions(): void
    {
        $instructor = User::create([
            'nama_lengkap' => 'Galih Instructor',
            'email' => 'galih3@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'verification_status' => 'approved',
        ]);

        $sekolah = Sekolah::create([
            'kodlan' => 'ERL99887',
            'namasekolah' => 'SD Permata Hati',
            'jenjang' => 'SD',
            'status' => 'aktif',
            'kec' => 'Kecamatan Test',
            'kotkab' => 'Kota Jakarta Timur',
            'kota' => 'JAKARTA TIMUR',
            'provinsi' => 'DKI Jakarta',
        ]);

        $ekskul = Ekstrakurikuler::create([
            'nama_program' => 'Coding Scratch',
            'sekolah_id' => $sekolah->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'kategori_program' => 'Coding Scratch',
            'total_siswa' => 15,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'tahun_ajaran' => '2025/2026',
            'status' => 'aktif',
            'created_by' => $instructor->id,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel' => 1,
            'nama_rombel' => 'Rombel 1',
            'jumlah_siswa' => 15,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $instructor->id,
        ]);

        // Session 1 completed
        $session1 = $rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session1->update(['status' => EkstrakurikulerSession::STATUS_SELESAI]);
        LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session1->id,
            'user_id_instruktur' => $instructor->id,
            'pertemuan_ke' => 1,
            'rombel' => $rombel->nama_rombel,
            'sekolah_kodlan' => $sekolah->kodlan,
            'jadwal_mengajar' => '2025-08-01',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Coding Scratch',
            'materi_pengajaran' => 'Pertemuan 1',
            'jumlah_siswa_hadir' => 10,
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
        ]);

        // Set session 2 as LIBUR and session 3 as DITUNDA
        $session2 = $rombel->sessions()->where('nomor_pertemuan', 2)->first();
        $session2->update(['status' => EkstrakurikulerSession::STATUS_LIBUR]);

        $session3 = $rombel->sessions()->where('nomor_pertemuan', 3)->first();
        $session3->update(['status' => EkstrakurikulerSession::STATUS_DITUNDA]);

        // Meeting 4 finished, but since sessions 2 & 3 were libur/ditunda, only 2 valid completed sessions exist (1 & 4)
        $session4 = $rombel->sessions()->where('nomor_pertemuan', 4)->first();
        $session4->update(['status' => EkstrakurikulerSession::STATUS_SELESAI]);

        $laporan4 = LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session4->id,
            'user_id_instruktur' => $instructor->id,
            'pertemuan_ke' => 4,
            'rombel' => $rombel->nama_rombel,
            'sekolah_kodlan' => $sekolah->kodlan,
            'jadwal_mengajar' => '2025-08-25',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Coding Scratch',
            'materi_pengajaran' => 'Pertemuan 4',
            'jumlah_siswa_hadir' => 10,
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
        ]);

        $service = new MilestoneNotificationService();
        $notif = $service->checkAndTriggerMilestoneNotification($session4, $laporan4);

        // Harus NULL karena baru 2 sesi mengajar yang selesai (pertemuan 2 & 3 libur/ditunda)
        $this->assertNull($notif, 'Milestone notification tidak boleh muncul jika baru < 4 sesi mengajar yang selesai');
    }
}

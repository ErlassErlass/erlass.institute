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

    public function test_admin_can_view_notification_center_and_toggle_read_unread(): void
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Super',
            'email' => 'admin_super@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_sistem',
            'status' => 'Aktif',
        ]);

        $notif = Notification::create([
            'type' => 'milestone_report',
            'title' => 'Test Milestone Notif',
            'message' => 'Detail milestone report test',
            'data' => [
                'pertemuan_ke' => 4,
                'sekolah_nama' => 'SMP Test',
                'instruktur_nama' => 'Test Ins',
                'rombel' => 'Rombel 1',
                'tanggal_mengajar_4' => [
                    ['pertemuan_ke' => 1, 'tanggal' => '01-08-2026'],
                    ['pertemuan_ke' => 2, 'tanggal' => '08-08-2026'],
                    ['pertemuan_ke' => 3, 'tanggal' => '15-08-2026'],
                    ['pertemuan_ke' => 4, 'tanggal' => '22-08-2026'],
                ],
            ],
            'is_read' => false,
            'read_at' => null,
        ]);

        // 1. Check index page
        $response = $this->actingAs($admin)->get(route('admin.notifications.index'));
        $response->assertStatus(200);
        $response->assertSee('Pusat Notifikasi & Arsip Milestone');
        $response->assertSee('Test Milestone Notif');

        // 2. Fetch unread API
        $respUnread = $this->actingAs($admin)->get(route('admin.notifications.unread', ['status' => 'unread']));
        $respUnread->assertStatus(200);
        $respUnread->assertJsonPath('unread_count', 1);

        // 3. Mark as read
        $respMarkRead = $this->actingAs($admin)->post(route('admin.notifications.read', $notif->id));
        $respMarkRead->assertStatus(200);
        $notif->refresh();
        $this->assertTrue($notif->is_read);
        $this->assertNotNull($notif->read_at);

        // 4. Fetch read API
        $respRead = $this->actingAs($admin)->get(route('admin.notifications.unread', ['status' => 'read']));
        $respRead->assertStatus(200);
        $respRead->assertJsonPath('unread_count', 0);

        // 5. Restore (Mark as unread)
        $respMarkUnread = $this->actingAs($admin)->post(route('admin.notifications.unread.single', $notif->id));
        $respMarkUnread->assertStatus(200);
        $notif->refresh();
        $this->assertFalse($notif->is_read);
        $this->assertNull($notif->read_at);
    }

    public function test_duplicate_milestone_notification_is_prevented_and_updates_existing(): void
    {
        $instructor = User::create([
            'nama_lengkap' => 'Sasqia Octaviana',
            'email' => 'sasqia@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'verification_status' => 'approved',
        ]);

        $sekolah = Sekolah::create([
            'kodlan' => 'ERL41600',
            'namasekolah' => 'SDS Muhammadiyah 06',
            'jenjang' => 'SD',
            'status' => 'aktif',
            'kec' => 'Tebet',
            'kotkab' => 'Kota Jakarta Selatan',
            'kota' => 'JAKARTA SELATAN',
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
            'hari' => 'kamis',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $instructor->id,
        ]);

        for ($i = 1; $i <= 4; $i++) {
            $s = $rombel->sessions()->where('nomor_pertemuan', $i)->first();
            $s->update(['status' => EkstrakurikulerSession::STATUS_SELESAI]);
            LaporanMengajar::create([
                'ekstrakurikuler_session_id' => $s->id,
                'user_id_instruktur' => $instructor->id,
                'pertemuan_ke' => $i,
                'rombel' => $rombel->nama_rombel,
                'sekolah_kodlan' => $sekolah->kodlan,
                'jadwal_mengajar' => "2026-08-0{$i}",
                'jam_mulai' => '13:00',
                'jam_selesai' => '14:30',
                'kategori_pengajaran' => 'Coding Scratch',
                'materi_pengajaran' => "Materi {$i}",
                'jumlah_siswa_hadir' => 13,
                'refleksi_siswa' => '-',
                'refleksi_capaian' => '-',
                'keaktifan' => 'aktif',
                'pemahaman_materi' => 'paham',
            ]);
        }

        $session4 = $rombel->sessions()->where('nomor_pertemuan', 4)->first();
        $laporan4 = $session4->laporanMengajar;

        $service = new MilestoneNotificationService();
        $notif1 = $service->checkAndTriggerMilestoneNotification($session4, $laporan4);
        $this->assertNotNull($notif1);

        $initialCount = Notification::where('type', 'milestone_report')->count();
        $this->assertEquals(1, $initialCount);

        // Second trigger for the same rombel and milestone must NOT duplicate
        $notif2 = $service->checkAndTriggerMilestoneNotification($session4, $laporan4);
        $this->assertNotNull($notif2);
        $this->assertEquals($notif1->id, $notif2->id);
        $this->assertEquals(1, Notification::where('type', 'milestone_report')->count());
    }

    public function test_recalibration_purges_premature_notifications(): void
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Test 2',
            'email' => 'adm2@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin_sistem',
            'status' => 'Aktif',
        ]);

        // Manually create a premature notification for a non-existent or incomplete rombel
        $prematureNotif = Notification::create([
            'type' => 'milestone_report',
            'title' => '🔔 Laporan Milestone Pertemuan Ke-4 Selesai',
            'message' => 'Premature alert',
            'data' => [
                'rombel_id' => 99999, // non-existent rombel
                'pertemuan_ke' => 4,
                'tanggal_mengajar_4' => [
                    ['pertemuan_ke' => 1, 'tanggal' => '20-08-2026'],
                    ['pertemuan_ke' => 2, 'tanggal' => '03-09-2026'],
                    ['pertemuan_ke' => 3, 'tanggal' => '03-09-2026'],
                    ['pertemuan_ke' => 4, 'tanggal' => '10-09-2026'],
                ]
            ],
            'is_read' => false,
        ]);

        $this->assertDatabaseHas('notifications', ['id' => $prematureNotif->id]);

        // Test POST /admin/notifications/recalibrate
        $response = $this->actingAs($admin)->post(route('admin.notifications.recalibrate'));
        $response->assertRedirect(route('admin.notifications.index'));
        $response->assertSessionHas('success');

        // Premature notification must be purged
        $this->assertDatabaseMissing('notifications', ['id' => $prematureNotif->id]);
    }

    public function test_sekolah_bayar_instruktur_skips_standard_milestone_notification(): void
    {
        $instructor = User::create([
            'nama_lengkap' => 'Instructor Bayar',
            'email' => 'bayar@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'verification_status' => 'approved',
        ]);

        $sekolah = Sekolah::create([
            'kodlan' => 'ERL99999',
            'namasekolah' => 'SDS Strada Dipamarga Test',
            'jenjang' => 'SD',
            'status' => 'Aktif',
            'kec' => 'Kecamatan Test',
            'kotkab' => 'Kota Jakarta Timur',
            'kota' => 'JAKARTA TIMUR',
            'provinsi' => 'DKI Jakarta',
            'is_sekolah_bayar_instruktur' => true,
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
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-11-30',
            'tahun_ajaran' => '2026/2027',
            'status' => 'aktif',
            'created_by' => $instructor->id,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel' => 1,
            'nama_rombel' => 'Rombel 1',
            'jumlah_siswa' => 15,
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2026-11-30',
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $instructor->id,
        ]);

        for ($i = 1; $i <= 4; $i++) {
            $s = $rombel->sessions()->where('nomor_pertemuan', $i)->first();
            $s->update([
                'status' => EkstrakurikulerSession::STATUS_SELESAI,
                'tanggal_pelaksanaan' => "2026-08-0{$i}",
            ]);
            LaporanMengajar::create([
                'ekstrakurikuler_session_id' => $s->id,
                'user_id_instruktur' => $instructor->id,
                'pertemuan_ke' => $i,
                'rombel' => $rombel->nama_rombel,
                'sekolah_kodlan' => $sekolah->kodlan,
                'jadwal_mengajar' => "2026-08-0{$i}",
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
        $laporan4 = $session4->laporanMengajar;

        $service = new MilestoneNotificationService();
        $notif = $service->checkAndTriggerMilestoneNotification($session4, $laporan4);

        // Standard milestone notification must be skipped (null)
        $this->assertNull($notif);
        $this->assertEquals(0, Notification::where('type', 'milestone_report')->count());

        // But monthly cutoff payout generation must create the monthly_school_payout notification
        $stats = $service->generateMonthlySchoolPayoutNotifications(\Carbon\Carbon::parse('2026-08-01'));
        $this->assertEquals(1, $stats['created']);

        $payoutNotif = Notification::where('type', 'monthly_school_payout')->first();
        $this->assertNotNull($payoutNotif);
        $this->assertTrue($payoutNotif->data['is_priority']);
        $this->assertEquals(4, $payoutNotif->data['total_sesi']);
        $this->assertEquals('2026-08', $payoutNotif->data['bulan_key']);
    }
}

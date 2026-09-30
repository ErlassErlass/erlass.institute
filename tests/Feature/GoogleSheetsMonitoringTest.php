<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GoogleSheetsMonitoringTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor;
    private Sekolah $sekolah;
    private Ekstrakurikuler $ekskul;
    private EkstrakurikulerRombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin_sistem',
            'email' => 'admin.test@erlass.institute',
        ]);

        $this->instructor = User::factory()->verifiedInstructor()->create([
            'nama_lengkap' => 'Budi Instruktur',
            'no_telephone' => '081234567890',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'SEK001',
            'namasekolah' => 'SMA Erlass Juara',
        ]);

        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'kategori_program' => 'Robotics Club',
        ]);

        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel Alpha',
            'nomor_rombel' => 1,
            'total_pertemuan' => 4,
            'tanggal_mulai' => now()->subDays(10)->toDateString(),
            'tanggal_selesai' => now()->addDays(20)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'jumlah_siswa' => 15,
            'status' => 'berlangsung',
        ]);
    }

    public function test_admin_can_access_google_sheets_dashboard_with_monitoring_tab(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.google-sheets.index'));

        $response->assertOk();
        $response->assertSee('11. Monitoring Belum Laporan');
        $response->assertSee('Unduh Excel (.xlsx)');
        $response->assertSee('Unduh CSV');
    }

    public function test_csv_export_filters_unreported_past_sessions_correctly(): void
    {
        // 1. Sesi terlambat 3 hari (Wajib muncul)
        $pastUnreportedSession = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();
        $pastUnreportedSession->update([
            'tanggal_terjadwal' => Carbon::today()->subDays(3)->toDateString(),
            'user_id_instruktur' => $this->instructor->id,
            'status' => 'terjadwal',
        ]);

        // 2. Sesi hari ini belum laporan (Wajib muncul)
        $todaySession = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 2)
            ->first();
        $todaySession->update([
            'tanggal_terjadwal' => Carbon::today()->toDateString(),
            'user_id_instruktur' => $this->instructor->id,
            'status' => 'berlangsung',
        ]);

        // 3. Sesi masa depan (Tidak boleh muncul)
        $futureSession = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 3)
            ->first();
        $futureSession->update([
            'tanggal_terjadwal' => Carbon::today()->addDays(5)->toDateString(),
            'user_id_instruktur' => $this->instructor->id,
            'status' => 'terjadwal',
        ]);

        // 4. Sesi kemarin tapi SUDAH ada laporan (Tidak boleh muncul)
        $reportedSession = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 4)
            ->first();
        $reportedSession->update([
            'tanggal_terjadwal' => Carbon::today()->subDays(1)->toDateString(),
            'user_id_instruktur' => $this->instructor->id,
            'status' => 'selesai',
        ]);
        LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $reportedSession->id,
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 4,
            'rombel' => $this->rombel->nama_rombel,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'jadwal_mengajar' => $reportedSession->tanggal_terjadwal,
            'materi_pengajaran' => 'Test Selesai',
            'jumlah_siswa_hadir' => 15,
        ]);

        // Unduh CSV
        $response = $this->actingAs($this->admin)->get(route('admin.google-sheets.export', 'monitoring_belum_laporan'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        
        $content = $response->getContent();

        // Cek sesi terlambat dan sesi hari ini muncul
        $this->assertStringContainsString((string) $pastUnreportedSession->id, $content);
        $this->assertStringContainsString('Terlambat 3 Hari', $content);
        $this->assertStringContainsString((string) $todaySession->id, $content);
        $this->assertStringContainsString('Hari Ini (Belum Laporan)', $content);
        $this->assertStringContainsString('Budi Instruktur', $content);
        $this->assertStringContainsString('081234567890', $content);
        $this->assertStringContainsString('SMA Erlass Juara', $content);

        // Cek sesi masa depan & yang sudah laporan TIDAK muncul
        $this->assertStringNotContainsString('/ekstrakurikuler/sessions/' . $futureSession->id, $content);
        $this->assertStringNotContainsString('/ekstrakurikuler/sessions/' . $reportedSession->id, $content);
    }

    public function test_admin_can_download_excel_monitoring_belum_laporan(): void
    {
        $session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();
        $session->update([
            'tanggal_terjadwal' => Carbon::today()->subDays(2)->toDateString(),
            'user_id_instruktur' => $this->instructor->id,
            'status' => 'terjadwal',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.google-sheets.export-excel', 'monitoring_belum_laporan'));

        $response->assertOk();
        $response->assertHeader('content-disposition');
        $this->assertStringContainsString('.xlsx', $response->headers->get('content-disposition'));
    }

    public function test_api_feed_includes_monitoring_belum_laporan(): void
    {
        $response = $this->getJson(route('api.google-sheets.feed', ['token' => 'erlass_sheets_sync_2026']));

        $response->assertOk();
        $response->assertJsonStructure([
            'success',
            'timestamp',
            'tabs' => [
                'Ringkasan_KPI',
                'Laporan_Mengajar',
                'Jadwal_Sesi_Ekskul',
                'Absensi_Siswa',
                'Rekap_Honor',
                'Rekap_Pertemuan_Ekskul',
                'Daftar_Program_Ekskul',
                'Rekap_Honor_Instruktur',
                'Profil_Instruktur',
                'Data_Siswa',
                'Monitoring_Belum_Laporan',
            ],
        ]);
    }
}

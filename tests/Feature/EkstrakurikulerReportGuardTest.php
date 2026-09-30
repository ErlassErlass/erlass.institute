<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstrakurikulerReportGuardTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private Sekolah $sekolah;
    private Ekstrakurikuler $ekskul;
    private EkstrakurikulerRombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->verifiedInstructor()->create([
            'nama_lengkap' => 'Arya Test',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'TEST001',
            'namasekolah' => 'Test School',
        ]);

        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
        ]);

        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel 1',
            'nomor_rombel' => 1,
            'total_pertemuan' => 2,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addDays(14)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'jumlah_siswa' => 0,
            'status' => 'berlangsung',
        ]);
    }

    public function test_auto_heal_when_session_status_is_selesai_without_report(): void
    {
        $session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();

        $session->update([
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'user_id_instruktur' => $this->instructor->id,
            'jam_mulai_aktual' => '08:00:00',
            'jam_selesai_aktual' => '09:30:00',
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('ekstrakurikuler.sessions.report.create', $session));

        $response->assertStatus(200);

        $sessionFresh = $session->fresh();
        $this->assertEquals(EkstrakurikulerSession::STATUS_BERLANGSUNG, $sessionFresh->status);
        $this->assertNull($sessionFresh->jam_selesai_aktual);
    }

    public function test_redirects_to_show_when_session_status_is_selesai_and_report_exists(): void
    {
        $session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();

        $session->update([
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'user_id_instruktur' => $this->instructor->id,
        ]);

        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'ekstrakurikuler_session_id' => $session->id,
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('ekstrakurikuler.sessions.report.create', $session));

        $response->assertRedirect(route('laporan-mengajar.show', $laporan));
        $response->assertSessionHas('info');
    }
}

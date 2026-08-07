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

class AdHocSessionAttendanceTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sekolah;
    protected $ekskul;
    protected $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $this->salesman = \App\Models\Salesman::factory()->create(['user_id' => $this->user->id]);
        $this->sekolah = Sekolah::factory()->create(['kodlan' => 'SEK123', 'namasekolah' => 'SD Negeri 1 Test']);
        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'user_id_sales' => $this->salesman->id,
            'kategori_program' => 'Coding Scratch',
            'status' => 'aktif',
        ]);
        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel 1',
            'nomor_rombel' => 1,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'hari' => 'senin',
            'jumlah_siswa' => 15,
        ]);
    }

    public function test_is_adhoc_helper_identifies_adhoc_reports_and_sessions()
    {
        $adhocReport = LaporanMengajar::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Rombel 1',
            'kategori_pengajaran' => 'Trial Class',
        ]);

        $this->assertTrue($adhocReport->isAdHoc());

        $regularReport = LaporanMengajar::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Rombel 1',
            'kategori_pengajaran' => 'Reguler',
        ]);

        $this->assertFalse($regularReport->isAdHoc());

        $adhocSession = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'nomor_pertemuan' => 0,
            'tanggal_terjadwal' => now()->toDateString(),
            'jam_mulai_terjadwal' => '08:00',
            'jam_selesai_terjadwal' => '09:30',
            'status' => 'terjadwal',
        ]);

        $this->assertTrue($adhocSession->isAdHoc());
    }

    public function test_adhoc_report_creation_redirects_to_show_without_absensi_prompt()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $file = \Illuminate\Http\Testing\File::image('foto.jpg');

        $response = $this->actingAs($this->user)
            ->post(route('laporan-mengajar.store'), [
                'user_id_instruktur' => $this->user->id,
                'sekolah_kodlan' => $this->sekolah->kodlan,
                'sekolah_nama' => $this->sekolah->namasekolah,
                'rombel' => 'Rombel 1',
                'pertemuan_ke' => 1,
                'kategori_pengajaran' => 'Trial Class',
                'materi_pengajaran' => 'Pengenalan Scratch Trial',
                'jadwal_mengajar' => now()->format('Y-m-d'),
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:30',
                'jumlah_siswa_hadir' => 15,
                'jumlah_siswa_tidak_hadir' => 0,
                'jumlah_siswa_keluar' => 0,
                'refleksi_siswa' => '-',
                'refleksi_capaian' => '-',
                'keaktifan' => 'aktif',
                'pemahaman_materi' => 'paham',
                'foto_kegiatan' => $file,
            ]);

        $response->assertSessionHasNoErrors();
        $laporan = LaporanMengajar::latest('id')->first();
        $this->assertNotNull($laporan);
        $response->assertRedirect(route('laporan-mengajar.show', $laporan));
        $response->assertSessionHas('success');
    }

    public function test_absensi_create_blocks_individual_attendance_for_adhoc_reports()
    {
        $adhocReport = LaporanMengajar::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Rombel 1',
            'kategori_pengajaran' => 'Sosialisasi',
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('laporan-mengajar.absensi.create', $adhocReport));

        $response->assertRedirect(route('laporan-mengajar.show', $adhocReport));
        $response->assertSessionHas('info');
    }
}

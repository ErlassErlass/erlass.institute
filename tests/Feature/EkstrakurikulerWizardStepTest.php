<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Ekstrakurikuler;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstrakurikulerWizardStepTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sekolah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'is_verified' => true,
        ]);
        \App\Models\Salesman::factory()->create(['user_id' => $this->user->id]);
        $this->sekolah = Sekolah::factory()->create(['kodlan' => 'SEK999', 'namasekolah' => 'SD Test Wizard']);
    }

    public function test_direct_url_access_to_uncompleted_step_redirects_to_first_incomplete_step()
    {
        // Session is empty, user tries to access Step 5 directly via URL
        $response = $this->actingAs($this->user)
            ->get(route('ekstrakurikuler.create.step', ['step' => 5]));

        // Should be redirected to Step 1
        $response->assertRedirect(route('ekstrakurikuler.create.step', ['step' => 1]));
        $response->assertSessionHas('warning', 'Silakan lengkapi langkah sebelumnya terlebih dahulu.');
    }

    public function test_step5_accepts_jam_mulai_without_jam_selesai_and_autocalculates_duration()
    {
        // Populate session with steps 1-4 completed
        $sessionData = [
            'kategori_program' => Ekstrakurikuler::KATEGORI_CODING_SCRATCH,
            'deskripsi' => 'Deskripsi test',
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'alamat_lengkap' => 'Jl. Pendidikan No 123, Jakarta',
            'google_maps_link' => 'https://maps.google.com',
            'jarak_km' => 10.5,
            'kepala_sekolah' => 'Dr. H. Kepala Sekolah',
            'penanggung_jawab' => 'Bpk. Penanggung Jawab',
            'no_telepon' => '081234567890',
            'email' => 'sekolah@test.com',
            'koneksi_internet' => 'ada',
            'proyektor' => 'ada',
            'kabel_hdmi' => 'ada',
            'kabel_vga' => 'ada',
            'kabel_roll' => 'ada',
            'total_siswa' => 20,
            'total_ruangan' => 1,
            'total_rombel' => 1,
        ];

        // Submit Step 5 (Rombel 1) with jam_mulai = 08:00 and NO jam_selesai
        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 5,
                'rombel_1_total_pertemuan' => 12,
                'rombel_1_jumlah_siswa' => 20,
                'rombel_1_tanggal_mulai' => now()->format('Y-m-d'),
                'rombel_1_tanggal_selesai' => now()->addMonths(3)->format('Y-m-d'),
                'rombel_1_hari' => 'senin',
                'rombel_1_jam_mulai' => '08:00',
                // jam_selesai deliberately omitted
            ]);

        $response->assertSessionHasNoErrors();

        // Verify stored session data auto-calculated jam_selesai to 09:30
        $savedData = session('ekstrakurikuler_form_data');
        $this->assertNotNull($savedData);
        $this->assertEquals('08:00', $savedData['rombels'][1]['jam_mulai']);
        $this->assertEquals('09:30', $savedData['rombels'][1]['jam_selesai']);
    }

    public function test_complete_ekstrakurikuler_creation_stores_successfully_to_database()
    {
        $salesman = \App\Models\Salesman::first();

        $sessionData = [
            'kategori_program' => Ekstrakurikuler::KATEGORI_CODING_SCRATCH,
            'user_id_sales' => $salesman->id,
            'region' => 'JAKARTA',
            'deskripsi' => 'Deskripsi test',
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'alamat_lengkap' => 'Jl. Pendidikan No 123, Jakarta',
            'google_maps_link' => 'https://maps.google.com',
            'jarak_km' => 10.5,
            'kepala_sekolah' => 'Dr. H. Kepala Sekolah',
            'penanggung_jawab' => 'Bpk. Penanggung Jawab',
            'no_telepon' => '081234567890',
            'koneksi_internet' => 'ada',
            'keterangan_internet' => 'WiFi Fast 100Mbps',
            'proyektor' => 'ada',
            'keterangan_proyektor' => 'HDMI 1080p',
            'kabel_hdmi' => 'ada',
            'kabel_vga' => 'ada',
            'kabel_roll' => 'ada',
            'keterangan_kabel' => 'Lengkap',
            'total_siswa' => 20,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'rombels' => [
                1 => [
                    'total_pertemuan' => 12,
                    'tanggal_mulai' => now()->format('Y-m-d'),
                    'tanggal_selesai' => now()->addMonths(3)->format('Y-m-d'),
                    'hari' => 'senin',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '09:30',
                    'jumlah_siswa' => 20,
                    'ruangan' => 'Ruang Lab 1',
                ]
            ]
        ];

        $formService = app(\App\Services\Ekstrakurikuler\EkstrakurikulerFormService::class);
        session(['ekstrakurikuler_form_data' => $sessionData]);

        $request = \Illuminate\Http\Request::create(route('ekstrakurikuler.store'), 'POST', [
            'submit_final' => 1,
            'final_confirmation' => 1,
        ]);
        $request->setUserResolver(fn() => $this->user);

        $ekskul = $formService->storeEkstrakurikuler($request);

        $this->assertNotNull($ekskul);
        $this->assertEquals($this->sekolah->kodlan, $ekskul->sekolah_kodlan);
        $this->assertEquals('WiFi Fast 100Mbps', $ekskul->keterangan_internet);
        $this->assertDatabaseHas('ekstrakurikuler', [
            'id' => $ekskul->id,
            'keterangan_internet' => 'WiFi Fast 100Mbps',
        ]);
    }
}

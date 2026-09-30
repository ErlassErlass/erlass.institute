<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Salesman;
use App\Models\Product;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PelatihanProgramTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sekolah;
    protected $salesman;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'is_verified' => true,
        ]);
        $this->salesman = Salesman::factory()->create(['user_id' => $this->user->id]);
        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'SEK-PELATIH-01',
            'namasekolah' => 'SD Pelatihan Jaya',
            'kota' => 'Jakarta Selatan',
        ]);

        // Seed products if needed
        Product::firstOrCreate(
            ['nama_produk' => 'Pelatihan Coding Scratch'],
            [
                'kode_produk' => 'PCR',
                'jenis' => 'Pelatihan',
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
            ]
        );

        Product::firstOrCreate(
            ['nama_produk' => 'Ekskul Coding Scratch'],
            [
                'kode_produk' => 'ECR',
                'jenis' => 'Ekskul',
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
                'is_aktif' => true,
            ]
        );
    }

    /**
     * Test step 1 renders Pelatihan and Ekstrakurikuler optgroups
     */
    public function test_create_step1_displays_pelatihan_and_ekskul_options()
    {
        $response = $this->actingAs($this->user)
            ->get(route('ekstrakurikuler.create.step', ['step' => 1]));

        $response->assertStatus(200);
        $response->assertSee('Program Pelatihan');
        $response->assertSee('Program Ekstrakurikuler');
        $response->assertSee('Pelatihan Coding Scratch');
        $response->assertSee('Ekskul Coding Scratch');
    }

    /**
     * Test single-day Pelatihan validation allows same start and end date
     */
    public function test_pelatihan_1_hari_accepts_same_start_and_end_date()
    {
        $sessionData = [
            'kategori_program' => 'Pelatihan Coding Scratch',
            'jenis_program' => 'pelatihan',
            'user_id_sales' => $this->salesman->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'alamat_lengkap' => 'Jl. Pelatihan No. 1, Jakarta Selatan',
            'jarak_km' => 5.0,
            'kepala_sekolah' => 'Kepala Sekolah',
            'penanggung_jawab' => 'PIC Pelatihan',
            'no_telepon' => '08123456789',
            'koneksi_internet' => 'ada',
            'proyektor' => 'ada',
            'kabel_hdmi' => 'ada',
            'kabel_vga' => 'ada',
            'kabel_roll' => 'ada',
            'total_siswa' => 30,
            'total_ruangan' => 1,
            'total_rombel' => 1,
        ];

        // 15 Oktober 2026 (Kamis)
        $targetDate = '2026-10-15';

        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 5,
                'rombel_1_total_pertemuan' => 1,
                'rombel_1_jumlah_siswa' => 30,
                'rombel_1_tanggal_mulai' => $targetDate,
                'rombel_1_tanggal_selesai' => $targetDate, // Tanggal awal dan akhir di hari yang sama!
                'rombel_1_hari' => 'kamis',
                'rombel_1_jam_mulai' => '08:00',
                'rombel_1_jam_selesai' => '16:00', // 8 jam (fleksibel)
            ]);

        $response->assertSessionHasNoErrors();
        $saved = session('ekstrakurikuler_form_data');
        $this->assertEquals($targetDate, $saved['rombels'][1]['tanggal_mulai']);
        $this->assertEquals($targetDate, $saved['rombels'][1]['tanggal_selesai']);
        $this->assertEquals(1, $saved['rombels'][1]['total_pertemuan']);
    }

    /**
     * Test Pelatihan allows flexible hours beyond 90 minutes (e.g. 8 hours)
     */
    public function test_pelatihan_accepts_flexible_hours_beyond_90_minutes()
    {
        $sessionData = [
            'kategori_program' => 'Pelatihan Coding Scratch',
            'jenis_program' => 'pelatihan',
            'total_rombel' => 1,
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 5,
                'rombel_1_total_pertemuan' => 1,
                'rombel_1_jumlah_siswa' => 25,
                'rombel_1_tanggal_mulai' => '2026-10-15',
                'rombel_1_tanggal_selesai' => '2026-10-15',
                'rombel_1_hari' => 'kamis',
                'rombel_1_jam_mulai' => '08:00',
                'rombel_1_jam_selesai' => '17:00', // 9 jam workshop
            ]);

        $response->assertSessionHasNoErrors();
    }

    /**
     * Test regular Ekskul still rejects duration over 90 minutes
     */
    public function test_regular_ekskul_still_rejects_duration_over_90_minutes()
    {
        $sessionData = [
            'kategori_program' => 'Ekskul Coding Scratch',
            'jenis_program' => 'ekstrakurikuler',
            'total_rombel' => 1,
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 5,
                'rombel_1_total_pertemuan' => 12,
                'rombel_1_jumlah_siswa' => 20,
                'rombel_1_tanggal_mulai' => '2026-10-12',
                'rombel_1_tanggal_selesai' => '2026-12-28',
                'rombel_1_hari' => 'senin',
                'rombel_1_jam_mulai' => '13:00',
                'rombel_1_jam_selesai' => '16:00', // 180 menit -> should fail
            ]);

        $response->assertSessionHasErrors(['rombel_1_jam_selesai']);
    }

    /**
     * Test full storage of single-day Pelatihan creates exactly 1 session on that day
     */
    public function test_complete_pelatihan_1_hari_stores_successfully_with_frekuensi_harian()
    {
        $targetDate = '2026-10-15'; // Kamis

        $sessionData = [
            'kategori_program' => 'Pelatihan Coding Scratch',
            'jenis_program' => 'pelatihan',
            'user_id_sales' => $this->salesman->id,
            'region' => 'JAKARTA',
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'alamat_lengkap' => 'Jl. Pelatihan No. 1, Jakarta Selatan',
            'jarak_km' => 5.0,
            'kepala_sekolah' => 'Kepala Sekolah',
            'penanggung_jawab' => 'PIC Pelatihan',
            'no_telepon' => '08123456789',
            'koneksi_internet' => 'ada',
            'proyektor' => 'ada',
            'kabel_hdmi' => 'ada',
            'kabel_vga' => 'ada',
            'kabel_roll' => 'ada',
            'total_siswa' => 30,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'rombels' => [
                1 => [
                    'total_pertemuan' => 1,
                    'tanggal_mulai' => $targetDate,
                    'tanggal_selesai' => $targetDate,
                    'hari' => 'kamis',
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '15:00',
                    'jumlah_siswa' => 30,
                    'ruangan' => 'Aula Utama',
                ],
            ],
        ];

        // Submit Final Step (Step 6 for 1 rombel)
        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 6,
                'submit_final' => 1,
                'final_confirmation' => 1,
            ]);

        $response->assertRedirect(route('ekstrakurikuler.index'));

        // Verify Database
        $ekskul = Ekstrakurikuler::where('kategori_program', 'Pelatihan Coding Scratch')->first();
        $this->assertNotNull($ekskul);
        $this->assertEquals(Ekstrakurikuler::JENIS_PELATIHAN, $ekskul->jenis_program);
        $this->assertTrue($ekskul->isPelatihan());
        $this->assertFalse($ekskul->isEkstrakurikuler());
        $this->assertEquals(EkstrakurikulerRombel::FREKUENSI_HARIAN, $ekskul->frekuensi);
        $this->assertEquals($targetDate, $ekskul->tanggal_mulai->toDateString());
        $this->assertEquals($targetDate, $ekskul->tanggal_selesai->toDateString());

        // Verify Rombel
        $rombel = $ekskul->rombels()->first();
        $this->assertNotNull($rombel);
        $this->assertEquals(1, $rombel->total_pertemuan);
        $this->assertEquals(EkstrakurikulerRombel::FREKUENSI_HARIAN, $rombel->frekuensi);
        $this->assertEquals($targetDate, $rombel->tanggal_mulai->toDateString());
        $this->assertEquals($targetDate, $rombel->tanggal_selesai->toDateString());

        // Verify exactly 1 session was created on the exact date
        $sessions = $rombel->sessions()->get();
        $this->assertCount(1, $sessions);
        $this->assertEquals($targetDate, $sessions->first()->tanggal_terjadwal->toDateString());
        $this->assertEquals('08:00', $sessions->first()->jam_mulai_terjadwal->format('H:i'));
        $this->assertEquals('15:00', $sessions->first()->jam_selesai_terjadwal->format('H:i'));
    }

    /**
     * Test multi-day Pelatihan (e.g. 3 days consecutive)
     */
    public function test_pelatihan_multi_hari_generates_daily_sessions()
    {
        $startDate = '2026-10-14'; // Rabu
        $endDate = '2026-10-16';   // Jumat (3 hari berturut-turut)

        $sessionData = [
            'kategori_program' => 'Pelatihan Coding Scratch',
            'jenis_program' => 'pelatihan',
            'user_id_sales' => $this->salesman->id,
            'region' => 'JAKARTA',
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'alamat_lengkap' => 'Jl. Pelatihan No. 1, Jakarta Selatan',
            'jarak_km' => 5.0,
            'kepala_sekolah' => 'Kepala Sekolah',
            'penanggung_jawab' => 'PIC Pelatihan',
            'no_telepon' => '08123456789',
            'koneksi_internet' => 'ada',
            'proyektor' => 'ada',
            'kabel_hdmi' => 'ada',
            'kabel_vga' => 'ada',
            'kabel_roll' => 'ada',
            'total_siswa' => 20,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'rombels' => [
                1 => [
                    'total_pertemuan' => 3,
                    'tanggal_mulai' => $startDate,
                    'tanggal_selesai' => $endDate,
                    'hari' => 'rabu',
                    'jam_mulai' => '09:00',
                    'jam_selesai' => '15:00',
                    'jumlah_siswa' => 20,
                ],
            ],
        ];

        $response = $this->actingAs($this->user)
            ->withSession(['ekstrakurikuler_form_data' => $sessionData])
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 6,
                'submit_final' => 1,
                'final_confirmation' => 1,
            ]);

        $response->assertRedirect(route('ekstrakurikuler.index'));

        $ekskul = Ekstrakurikuler::where('kategori_program', 'Pelatihan Coding Scratch')->first();
        $this->assertNotNull($ekskul);
        $rombel = $ekskul->rombels()->first();
        $sessions = $rombel->sessions()->orderBy('nomor_pertemuan')->get();

        $this->assertCount(3, $sessions);
        $this->assertEquals('2026-10-14', $sessions[0]->tanggal_terjadwal->toDateString());
        $this->assertEquals('2026-10-15', $sessions[1]->tanggal_terjadwal->toDateString());
        $this->assertEquals('2026-10-16', $sessions[2]->tanggal_terjadwal->toDateString());
    }
}

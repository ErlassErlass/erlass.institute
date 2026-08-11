<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GpsCheckinTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private EkstrakurikulerSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->instructor = User::factory()->create([
            'role' => 'instruktur',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);

        $sekolah = Sekolah::create([
            'kodlan'      => 'SCH-TEST-GPS',
            'namasekolah' => 'SD Test GPS',
            'jenjang'     => 'SD',
            'status'      => 'Aktif',
            'kec'         => 'Kec Test',
            'kotkab'      => 'Kota Test',
            'kota'        => 'Kota Test',
            'provinsi'    => 'Jawa Barat',
            'latitude'    => -6.2000000,
            'longitude'   => 106.8166660,
        ]);

        $ekskul = Ekstrakurikuler::create([
            'nama_program' => 'Coding Robotik',
            'sekolah_id' => $sekolah->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'kategori_program' => 'Coding Robotik',
            'total_siswa' => 10,
            'total_ruangan' => 1,
            'total_rombel' => 1,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'total_pertemuan' => 12,
            'status' => 'aktif',
            'created_by' => $this->instructor->id,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Rombel GPS 1',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'jumat',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 12,
            'user_id_instruktur' => $this->instructor->id,
        ]);

        $this->session = $rombel->sessions()->first();
        if (! $this->session) {
            $this->session = EkstrakurikulerSession::create([
                'ekstrakurikuler_id' => $ekskul->id,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'nomor_pertemuan' => 1,
                'tanggal_terjadwal' => now()->toDateString(),
                'jam_mulai_terjadwal' => '13:00',
                'jam_selesai_terjadwal' => '14:30',
                'user_id_instruktur' => $this->instructor->id,
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
            ]);
        } else {
            $this->session->update([
                'user_id_instruktur' => $this->instructor->id,
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
            ]);
        }
    }

    public function test_instructor_can_checkin_with_valid_gps_radius(): void
    {
        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2001000, // Very close to -6.2000000 (~11 meters)
                'longitude' => 106.8166660,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->session->refresh();
        $this->assertEquals('valid', $this->session->checkin_status_radius);
        $this->assertLessThanOrEqual(500, $this->session->checkin_distance_meters);
        $this->assertNotNull($this->session->checkin_photo_path);
    }
}

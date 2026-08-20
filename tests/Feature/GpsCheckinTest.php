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
    private Ekstrakurikuler $ekskul;

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
        ]);

        $this->ekskul = Ekstrakurikuler::create([
            'nama_program' => 'Coding Robotik',
            'sekolah_id' => $sekolah->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'kategori_program' => 'Coding Robotik',
            'latitude' => -6.2854970,
            'longitude' => 106.8982750,
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
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel GPS 1',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'jumat',
            'jam_mulai' => now()->subMinutes(5)->format('H:i'),
            'jam_selesai' => now()->addMinutes(85)->format('H:i'),
            'total_pertemuan' => 12,
            'user_id_instruktur' => $this->instructor->id,
        ]);

        $this->session = $rombel->sessions()->first();
        if (! $this->session) {
            $this->session = EkstrakurikulerSession::create([
                'ekstrakurikuler_id' => $this->ekskul->id,
                'ekstrakurikuler_rombel_id' => $rombel->id,
                'nomor_pertemuan' => 1,
                'tanggal_terjadwal' => now()->toDateString(),
                'jam_mulai_terjadwal' => now()->subMinutes(5)->format('H:i'),
                'jam_selesai_terjadwal' => now()->addMinutes(85)->format('H:i'),
                'user_id_instruktur' => $this->instructor->id,
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
            ]);
        } else {
            $this->session->update([
                'user_id_instruktur' => $this->instructor->id,
                'tanggal_terjadwal' => now()->toDateString(),
                'jam_mulai_terjadwal' => now()->subMinutes(5)->format('H:i'),
                'jam_selesai_terjadwal' => now()->addMinutes(85)->format('H:i'),
                'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
            ]);
        }
    }

    public function test_instructor_can_checkin_with_valid_gps_radius(): void
    {
        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2853940, // ~57m from target (-6.285497, 106.898275)
                'longitude' => 106.8987810,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->session->refresh();
        $this->assertEquals('valid', $this->session->checkin_status_radius);
        $this->assertLessThanOrEqual(500, $this->session->checkin_distance_meters);
        $this->assertNotNull($this->session->checkin_photo_path);
    }

    public function test_instructor_checkin_out_of_bounds(): void
    {
        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.1000000, // Very far away (~20km)
                'longitude' => 106.8000000,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $this->session->refresh();
        $this->assertEquals('out_of_bounds', $this->session->checkin_status_radius);
        $this->assertGreaterThan(500, $this->session->checkin_distance_meters);
    }

    public function test_instructor_checkin_when_no_coordinates_configured(): void
    {
        $this->ekskul->updateQuietly([
            'latitude' => null,
            'longitude' => null,
            'google_maps_link' => null,
        ]);

        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2000000,
                'longitude' => 106.8000000,
                'photo' => $file,
            ]);

        $response->assertRedirect();

        $this->session->refresh();
        $this->assertEquals('unverified', $this->session->checkin_status_radius);
        $this->assertNull($this->session->checkin_distance_meters);
    }

    public function test_instructor_cannot_checkin_before_10_minutes_window(): void
    {
        // Set scheduled time to 2 hours in the future
        $this->session->updateQuietly([
            'jam_mulai_terjadwal' => now()->addHours(2)->format('H:i'),
            'jam_selesai_terjadwal' => now()->addHours(3)->addMinutes(30)->format('H:i'),
        ]);

        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2854970,
                'longitude' => 106.8982750,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('warning');

        $this->session->refresh();
        $this->assertNull($this->session->checkin_lat);
        $this->assertEquals(EkstrakurikulerSession::STATUS_TERJADWAL, $this->session->status);
    }

    public function test_admin_can_bypass_checkin_window(): void
    {
        $admin = \App\Models\User::factory()->create(['role' => 'admin']);

        // Set scheduled time to 2 hours in the future
        $this->session->updateQuietly([
            'jam_mulai_terjadwal' => now()->addHours(2)->format('H:i'),
            'jam_selesai_terjadwal' => now()->addHours(3)->addMinutes(30)->format('H:i'),
        ]);

        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($admin)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2853940,
                'longitude' => 106.8987810,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->session->refresh();
        $this->assertNotNull($this->session->checkin_lat);
    }

    public function test_checkin_with_mock_accuracy_flags_anomaly(): void
    {
        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2853940,
                'longitude' => 106.8987810,
                'accuracy' => 0, // Mock anomaly
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $this->session->refresh();
        $this->assertTrue((bool) $this->session->checkin_mock_suspected);
    }

    public function test_checkin_with_impossible_speed_flags_teleportation(): void
    {
        // Buat sesi sebelumnya 5 menit lalu di lokasi 40 km jauhnya
        EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->session->ekstrakurikuler_rombel_id,
            'nomor_pertemuan' => 99,
            'tanggal_terjadwal' => now()->toDateString(),
            'jam_mulai_terjadwal' => now()->subMinutes(30)->format('H:i'),
            'jam_selesai_terjadwal' => now()->subMinutes(10)->format('H:i'),
            'user_id_instruktur' => $this->instructor->id,
            'checkin_lat' => -6.600000, // Bogor (~40 km dari Jakarta)
            'checkin_lng' => 106.800000,
            'checkin_distance_meters' => 10,
            'checkin_status_radius' => 'valid',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'updated_at' => now()->subMinutes(5), // Baru 5 menit lalu
        ]);

        $file = UploadedFile::fake()->image('checkin.jpg');

        $response = $this->actingAs($this->instructor)
            ->post(route('ekstrakurikuler.sessions.checkin', $this->session), [
                'latitude' => -6.2853940, // Jakarta
                'longitude' => 106.8987810,
                'accuracy' => 15,
                'photo' => $file,
            ]);

        $response->assertRedirect();
        $this->session->refresh();
        $this->assertTrue((bool) $this->session->checkin_mock_suspected);
    }
}

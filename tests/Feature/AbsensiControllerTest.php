<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\LaporanMengajar;
use App\Models\Absensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AbsensiControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $admin;
    private Sekolah $sekolah;
    private Siswa $siswa1;
    private Siswa $siswa2;
    private LaporanMengajar $laporanMengajar;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->instructor = User::factory()->create([
            'role' => 'instruktur',
            'nama_lengkap' => 'Test Instructor'
        ]);
        
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'nama_lengkap' => 'Test Admin'
        ]);
        
        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'TEST001',
            'namasekolah' => 'Test School'
        ]);
        
        $this->siswa1 = Siswa::factory()->create([
            'nama_lengkap' => 'Siswa 1',
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1'
        ]);
        
        $this->siswa2 = Siswa::factory()->create([
            'nama_lengkap' => 'Siswa 2',
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1'
        ]);
        
        $this->laporanMengajar = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1',
            'jadwal_mengajar' => Carbon::today()->format('Y-m-d')
        ]);
    }

    public function test_guest_cannot_access_absensi_create(): void
    {
        $response = $this->get(route('laporan-mengajar.absensi.create', $this->laporanMengajar));
        
        $response->assertRedirect(route('login'));
    }

    public function test_instructor_can_access_absensi_create_for_own_laporan(): void
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.absensi.create', $this->laporanMengajar));
        
        $response->assertStatus(200);
        $response->assertViewIs('absensi.create');
        $response->assertViewHas('laporanMengajar', $this->laporanMengajar);
        $response->assertViewHas('siswas');
    }

    public function test_instructor_cannot_access_absensi_create_for_others_laporan(): void
    {
        $otherInstructor = User::factory()->create(['role' => 'instruktur']);
        $otherLaporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $otherInstructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.absensi.create', $otherLaporan));
        
        $response->assertStatus(403);
    }

    public function test_admin_can_access_absensi_create_for_any_laporan(): void
    {
        $response = $this->actingAs($this->admin)
            ->get(route('laporan-mengajar.absensi.create', $this->laporanMengajar));
        
        $response->assertStatus(200);
        $response->assertViewIs('absensi.create');
    }

    public function test_absensi_create_shows_correct_students(): void
    {
        // Create student in different rombel (should not appear)
        $siswa3 = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'B1'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.absensi.create', $this->laporanMengajar));
        
        $response->assertStatus(200);
        
        $siswas = $response->viewData('siswas');
        $this->assertCount(2, $siswas); // Only siswa1 and siswa2 from rombel A1
        $this->assertTrue($siswas->contains('id', $this->siswa1->id));
        $this->assertTrue($siswas->contains('id', $this->siswa2->id));
        $this->assertFalse($siswas->contains('id', $siswa3->id));
    }

    public function test_can_store_new_absensi(): void
    {
        $absensiData = [
            'absensi' => [
                $this->siswa1->id => 1, // Present
                $this->siswa2->id => 0, // Absent
            ]
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);
        
        $response->assertRedirect(route('laporan-mengajar.show', $this->laporanMengajar));
        $response->assertSessionHas('success');

        // Verify attendance records were created
        $this->assertDatabaseHas('absensi', [
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true
        ]);

        $this->assertDatabaseHas('absensi', [
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false
        ]);

        // Verify laporan statistics were updated
        $this->laporanMengajar->refresh();
        $this->assertEquals(1, $this->laporanMengajar->jumlah_siswa_hadir);
        $this->assertEquals(1, $this->laporanMengajar->jumlah_siswa_tidak_hadir);
    }

    public function test_can_update_existing_absensi(): void
    {
        // Create initial attendance
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true
        ]);

        // Update attendance
        $absensiData = [
            'absensi' => [
                $this->siswa1->id => 0, // Change to absent
                $this->siswa2->id => 1, // Add new present
            ]
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);
        
        $response->assertRedirect(route('laporan-mengajar.show', $this->laporanMengajar));

        // Verify attendance was updated
        $this->assertDatabaseHas('absensi', [
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => false // Updated
        ]);

        $this->assertDatabaseHas('absensi', [
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => true // Added
        ]);
    }

    public function test_validates_required_absensi_data(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), []);
        
        $response->assertSessionHasErrors(['absensi']);
    }

    public function test_validates_absensi_array_format(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), [
                'absensi' => 'invalid_format'
            ]);
        
        $response->assertSessionHasErrors(['absensi']);
    }

    public function test_validates_absensi_boolean_values(): void
    {
        $absensiData = [
            'absensi' => [
                $this->siswa1->id => 'invalid_boolean'
            ]
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);
        
        $response->assertSessionHasErrors(['absensi.*']);
    }

    public function test_validates_student_exists(): void
    {
        $absensiData = [
            'absensi' => [
                99999 => 1 // Non-existent student ID
            ]
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);
        
        $response->assertSessionHasErrors(['absensi']);
    }

    public function test_transaction_rollback_on_error(): void
    {
        // Mock an error condition by creating invalid data
        $absensiData = [
            'absensi' => [
                $this->siswa1->id => 1,
                $this->siswa2->id => 1,
            ]
        ];

        // Force database error by setting invalid foreign key
        LaporanMengajar::where('id', $this->laporanMengajar->id)->delete();

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Verify no partial data was saved
        $this->assertDatabaseCount('absensi', 0);
    }

    public function test_calculates_dropout_count(): void
    {
        // Create multiple reports for dropout calculation
        $reports = [];
        for ($i = 0; $i < 4; $i++) {
            $reports[] = LaporanMengajar::factory()->create([
                'user_id_instruktur' => $this->instructor->id,
                'sekolah_kodlan' => 'TEST001',
                'rombel' => 'A1',
                'jadwal_mengajar' => Carbon::today()->subDays($i)->format('Y-m-d')
            ]);
        }

        // Make siswa1 absent in last 3 sessions (should be dropout)
        foreach (array_slice($reports, 0, 3) as $report) {
            Absensi::create([
                'laporan_mengajar_id' => $report->id,
                'siswa_id' => $this->siswa1->id,
                'hadir' => false
            ]);
        }

        $absensiData = [
            'absensi' => [
                $this->siswa1->id => 0, // Another absence
                $this->siswa2->id => 1,
            ]
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.absensi.store', $this->laporanMengajar), $absensiData);
        
        $response->assertRedirect();

        // Verify dropout count was calculated
        $this->laporanMengajar->refresh();
        $this->assertEquals(1, $this->laporanMengajar->jumlah_siswa_keluar);
    }

    public function test_shows_existing_attendance_in_create_form(): void
    {
        // Create existing attendance
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.absensi.create', $this->laporanMengajar));
        
        $response->assertStatus(200);
        
        $existingAbsensi = $response->viewData('existingAbsensi');
        $this->assertEquals(1, $existingAbsensi[$this->siswa1->id]);
        $this->assertArrayNotHasKey($this->siswa2->id, $existingAbsensi->toArray());
    }
}
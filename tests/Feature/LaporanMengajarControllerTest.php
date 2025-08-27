<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\LaporanMengajar;
use App\Http\Requests\StoreLaporanMengajarRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanMengajarControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $admin;
    private User $otherInstructor;
    private Sekolah $sekolah;

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
        
        $this->otherInstructor = User::factory()->create([
            'role' => 'instruktur',
            'nama_lengkap' => 'Other Instructor'
        ]);
        
        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'TEST001',
            'namasekolah' => 'Test School',
            'kota' => 'Test City',
            'kec' => 'Test District'
        ]);
    }

    public function test_guest_cannot_access_laporan_mengajar(): void
    {
        $response = $this->get(route('laporan-mengajar.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_instructor_can_view_index(): void
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('laporan-mengajar.index');
    }

    public function test_instructor_sees_only_own_laporan(): void
    {
        // Create laporan for different instructors
        $ownLaporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);
        
        $otherLaporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->otherInstructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.index'));
        
        $response->assertStatus(200);
        $laporans = $response->viewData('laporans');
        
        $this->assertTrue($laporans->contains('id', $ownLaporan->id));
        $this->assertFalse($laporans->contains('id', $otherLaporan->id));
    }

    public function test_admin_sees_all_laporan(): void
    {
        $laporan1 = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);
        
        $laporan2 = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->otherInstructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('laporan-mengajar.index'));
        
        $response->assertStatus(200);
        $laporans = $response->viewData('laporans');
        
        $this->assertTrue($laporans->contains('id', $laporan1->id));
        $this->assertTrue($laporans->contains('id', $laporan2->id));
    }

    public function test_can_create_laporan_mengajar(): void
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.create'));
        
        $response->assertStatus(200);
        $response->assertViewIs('laporan-mengajar.create');
        $response->assertViewHas('instructors');
    }

    public function test_can_store_valid_laporan_mengajar(): void
    {
        Storage::fake('public');
        
        $fotoKegiatan = UploadedFile::fake()->image('kegiatan.jpg', 600, 400);
        $fotoAbsensi = UploadedFile::fake()->image('absensi.jpg', 600, 400);

        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'rombel' => 'A1',
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
            'sekolah_nama' => 'Test School',
            'sekolah_kota' => 'Test City',
            'sekolah_kecamatan' => 'Test District',
            'foto_kegiatan' => $fotoKegiatan,
            'foto_absensi_siswa' => $fotoAbsensi,
            'refleksi_siswa' => 'Good participation',
            'refleksi_capaian' => 'Target achieved',
            'keaktifan' => 8,
            'pemahaman_materi' => 7
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertRedirect(route('laporan-mengajar.index'));
        $response->assertSessionHas('success');

        // Verify database record
        $this->assertDatabaseHas('laporan_mengajar', [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'materi_pengajaran' => 'Test Material'
        ]);

        // Verify files were stored
        $laporan = LaporanMengajar::latest()->first();
        Storage::disk('public')->assertExists($laporan->foto_kegiatan);
        Storage::disk('public')->assertExists($laporan->foto_absensi_siswa);
    }

    public function test_validates_required_fields(): void
    {
        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), []);
        
        $response->assertSessionHasErrors([
            'user_id_instruktur',
            'pertemuan_ke',
            'sekolah_kodlan',
            'jadwal_mengajar'
        ]);
    }

    public function test_validates_pertemuan_ke_range(): void
    {
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 51, // Above max
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y')
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertSessionHasErrors(['pertemuan_ke']);
    }

    public function test_validates_time_sequence(): void
    {
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '08:00', // Before start time
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertSessionHasErrors(['jam_selesai']);
    }

    public function test_validates_date_not_too_old(): void
    {
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->subDays(8)->format('d/m/Y'), // 8 days ago
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertSessionHasErrors(['jadwal_mengajar']);
    }

    public function test_validates_instructor_not_same_as_assistant(): void
    {
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'user_id_assisten' => $this->instructor->id, // Same as instructor
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y')
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertSessionHasErrors(['user_id_assisten']);
    }

    public function test_validates_file_uploads(): void
    {
        Storage::fake('public');
        
        $oversizedFile = UploadedFile::fake()->create('large.jpg', 3000); // 3MB
        $invalidFile = UploadedFile::fake()->create('document.pdf', 500);

        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'foto_kegiatan' => $oversizedFile,
            'foto_absensi_siswa' => $invalidFile
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);
        
        $response->assertSessionHasErrors(['foto_kegiatan', 'foto_absensi_siswa']);
    }

    public function test_can_view_laporan_detail(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.show', $laporan));
        
        $response->assertStatus(200);
        $response->assertViewIs('laporan-mengajar.show');
        $response->assertViewHas('laporan', $laporan);
    }

    public function test_instructor_cannot_view_others_laporan(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->otherInstructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.show', $laporan));
        
        $response->assertStatus(403);
    }

    public function test_can_edit_own_laporan(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.edit', $laporan));
        
        $response->assertStatus(200);
        $response->assertViewIs('laporan-mengajar.edit');
        $response->assertViewHas('laporan', $laporan);
    }

    public function test_can_update_laporan(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'materi_pengajaran' => 'Original Material'
        ]);

        $updateData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => $laporan->pertemuan_ke,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::parse($laporan->jadwal_mengajar)->format('d/m/Y'),
            'materi_pengajaran' => 'Updated Material'
        ];

        $response = $this->actingAs($this->instructor)
            ->put(route('laporan-mengajar.update', $laporan), $updateData);
        
        $response->assertRedirect(route('laporan-mengajar.show', $laporan));
        
        $laporan->refresh();
        $this->assertEquals('Updated Material', $laporan->materi_pengajaran);
    }

    public function test_can_delete_laporan(): void
    {
        Storage::fake('public');
        
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'foto_kegiatan' => 'laporan_mengajar/test.jpg'
        ]);

        // Create fake file
        Storage::disk('public')->put('laporan_mengajar/test.jpg', 'fake content');

        $response = $this->actingAs($this->instructor)
            ->delete(route('laporan-mengajar.destroy', $laporan));
        
        $response->assertRedirect(route('laporan-mengajar.index'));
        
        $this->assertModelMissing($laporan);
        Storage::disk('public')->assertMissing('laporan_mengajar/test.jpg');
    }

    public function test_search_functionality(): void
    {
        $laporan1 = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'materi_pengajaran' => 'Mathematics'
        ]);
        
        $laporan2 = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'materi_pengajaran' => 'Science'
        ]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.search', ['query' => 'Mathematics']));
        
        $response->assertStatus(200);
        $response->assertJsonFragment(['materi_pengajaran' => 'Mathematics']);
        $response->assertJsonMissing(['materi_pengajaran' => 'Science']);
    }

    public function test_export_functionality(): void
    {
        LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        // Test Excel export
        $response = $this->actingAs($this->admin)
            ->get(route('laporan-mengajar.export', ['format' => 'excel']));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        // Test PDF export
        $response = $this->actingAs($this->admin)
            ->get(route('laporan-mengajar.export', ['format' => 'pdf']));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }
}
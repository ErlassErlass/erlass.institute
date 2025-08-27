<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Absensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaporanMengajarTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;
    private User $assistant;
    private Sekolah $sekolah;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->instructor = User::factory()->create([
            'role' => 'instruktur'
        ]);
        
        $this->assistant = User::factory()->create([
            'role' => 'instruktur'
        ]);
        
        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'TEST001'
        ]);
    }

    public function test_can_create_laporan_mengajar(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'materi_pengajaran' => 'Test Material'
        ]);

        $this->assertDatabaseHas('laporan_mengajar', [
            'id' => $laporan->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'materi_pengajaran' => 'Test Material'
        ]);
    }

    public function test_belongs_to_instructor(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id
        ]);

        $this->assertInstanceOf(User::class, $laporan->instruktur);
        $this->assertEquals($this->instructor->id, $laporan->instruktur->id);
    }

    public function test_belongs_to_assistant(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'user_id_assisten' => $this->assistant->id
        ]);

        $this->assertInstanceOf(User::class, $laporan->asisten);
        $this->assertEquals($this->assistant->id, $laporan->asisten->id);
    }

    public function test_belongs_to_sekolah(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'sekolah_kodlan' => 'TEST001'
        ]);

        $this->assertInstanceOf(Sekolah::class, $laporan->sekolah);
        $this->assertEquals('TEST001', $laporan->sekolah->kodlan);
    }

    public function test_has_many_absensis(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $siswa = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001'
        ]);

        $absensi = Absensi::create([
            'laporan_mengajar_id' => $laporan->id,
            'siswa_id' => $siswa->id,
            'hadir' => true
        ]);

        $this->assertCount(1, $laporan->absensis);
        $this->assertInstanceOf(Absensi::class, $laporan->absensis->first());
        $this->assertEquals($absensi->id, $laporan->absensis->first()->id);
    }

    public function test_fillable_attributes(): void
    {
        $attributes = [
            'user_id_instruktur' => $this->instructor->id,
            'user_id_assisten' => $this->assistant->id,
            'pertemuan_ke' => 1,
            'rombel' => 'A1',
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
            'sekolah_nama' => 'Test School',
            'sekolah_kota' => 'Test City',
            'sekolah_kecamatan' => 'Test District',
            'jumlah_siswa_hadir' => 10,
            'jumlah_siswa_keluar' => 0,
            'jumlah_siswa_tidak_hadir' => 2,
            'refleksi_siswa' => 'Good participation',
            'refleksi_capaian' => 'Target achieved',
            'keaktifan' => 8,
            'pemahaman_materi' => 7
        ];

        $laporan = LaporanMengajar::create($attributes);

        foreach ($attributes as $key => $value) {
            if ($key === 'jadwal_mengajar') {
                $this->assertEquals($value->format('Y-m-d'), $laporan->{$key});
            } else {
                $this->assertEquals($value, $laporan->{$key});
            }
        }
    }

    public function test_default_attributes(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id
        ]);

        $this->assertEquals(0, $laporan->jumlah_siswa_hadir);
        $this->assertEquals(0, $laporan->jumlah_siswa_tidak_hadir);
        $this->assertEquals(0, $laporan->jumlah_siswa_keluar);
    }

    public function test_guarded_attributes(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id
        ]);

        // Test that guarded attributes cannot be mass assigned
        $originalId = $laporan->id;
        $originalCreatedAt = $laporan->created_at;
        $originalUpdatedAt = $laporan->updated_at;

        $laporan->update([
            'id' => 999,
            'created_at' => Carbon::now()->subYear(),
            'updated_at' => Carbon::now()->subYear(),
            'materi_pengajaran' => 'Updated Material'
        ]);

        $laporan->refresh();

        $this->assertEquals($originalId, $laporan->id);
        $this->assertEquals($originalCreatedAt->format('Y-m-d H:i:s'), $laporan->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals('Updated Material', $laporan->materi_pengajaran);
    }

    public function test_file_cleanup_on_deletion(): void
    {
        Storage::fake('public');
        
        // Create files
        $fotoKegiatan = 'laporan_mengajar/test_kegiatan.jpg';
        $fotoAbsensi = 'laporan_mengajar_absensi/test_absensi.jpg';
        
        Storage::disk('public')->put($fotoKegiatan, 'fake image content');
        Storage::disk('public')->put($fotoAbsensi, 'fake image content');

        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'foto_kegiatan' => $fotoKegiatan,
            'foto_absensi_siswa' => $fotoAbsensi
        ]);

        // Verify files exist
        Storage::disk('public')->assertExists($fotoKegiatan);
        Storage::disk('public')->assertExists($fotoAbsensi);

        // Delete the laporan
        $laporan->delete();

        // Verify files are deleted
        Storage::disk('public')->assertMissing($fotoKegiatan);
        Storage::disk('public')->assertMissing($fotoAbsensi);
    }

    public function test_calculates_attendance_from_relationships(): void
    {
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001'
        ]);

        $siswa1 = Siswa::factory()->create(['sekolah_kodlan' => 'TEST001']);
        $siswa2 = Siswa::factory()->create(['sekolah_kodlan' => 'TEST001']);
        $siswa3 = Siswa::factory()->create(['sekolah_kodlan' => 'TEST001']);

        // Create attendance records
        Absensi::create([
            'laporan_mengajar_id' => $laporan->id,
            'siswa_id' => $siswa1->id,
            'hadir' => true
        ]);
        
        Absensi::create([
            'laporan_mengajar_id' => $laporan->id,
            'siswa_id' => $siswa2->id,
            'hadir' => true
        ]);
        
        Absensi::create([
            'laporan_mengajar_id' => $laporan->id,
            'siswa_id' => $siswa3->id,
            'hadir' => false
        ]);

        $presentCount = $laporan->absensis()->where('hadir', true)->count();
        $absentCount = $laporan->absensis()->where('hadir', false)->count();

        $this->assertEquals(2, $presentCount);
        $this->assertEquals(1, $absentCount);
    }

    public function test_requires_mandatory_fields(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create without required foreign key
        LaporanMengajar::create([
            'materi_pengajaran' => 'Test Material'
            // Missing user_id_instruktur which should be required
        ]);
    }
}
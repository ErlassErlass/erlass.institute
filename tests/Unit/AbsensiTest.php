<?php

namespace Tests\Unit;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiTest extends TestCase
{
    use RefreshDatabase;

    private LaporanMengajar $laporanMengajar;

    private Siswa $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $instructor = User::factory()->create(['role' => 'instruktur']);
        $sekolah = Sekolah::factory()->create(['kodlan' => 'TEST001']);

        $this->laporanMengajar = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $instructor->id,
            'sekolah_kodlan' => 'TEST001',
        ]);

        $this->siswa = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001',
        ]);
    }

    public function test_can_create_absensi(): void
    {
        $absensi = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $this->assertDatabaseHas('absensi', [
            'id' => $absensi->id,
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'status' => 'hadir',
        ]);
    }

    public function test_belongs_to_laporan_mengajar(): void
    {
        $absensi = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $this->assertInstanceOf(LaporanMengajar::class, $absensi->laporanMengajar);
        $this->assertEquals($this->laporanMengajar->id, $absensi->laporanMengajar->id);
    }

    public function test_belongs_to_siswa(): void
    {
        $absensi = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $this->assertInstanceOf(Siswa::class, $absensi->siswa);
        $this->assertEquals($this->siswa->id, $absensi->siswa->id);
    }

    public function test_fillable_attributes(): void
    {
        $attributes = [
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => false,
        ];

        $absensi = Absensi::create($attributes);

        foreach ($attributes as $key => $value) {
            $this->assertEquals($value, $absensi->{$key});
        }
    }

    public function test_hadir_is_cast_to_boolean(): void
    {
        // Test with integer 1
        $absensi1 = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => 1,
        ]);

        $this->assertTrue((bool)$absensi1->hadir);
        $this->assertTrue($absensi1->hadir === true);

        // Delete before next test to avoid unique constraint violation
        $absensi1->delete();

        // Test with integer 0
        $absensi2 = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => 0,
        ]);

        $this->assertFalse((bool)$absensi2->hadir);
        $this->assertTrue($absensi2->hadir === false);

        $absensi2->delete();

        // Test with string 'true'
        $absensi3 = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => 'true',
        ]);

        $this->assertTrue((bool)$absensi3->hadir);
        $this->assertTrue($absensi3->hadir === true);
    }

    public function test_guarded_attributes(): void
    {
        $absensi = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $originalId = $absensi->id;
        $originalCreatedAt = $absensi->created_at;

        // Try to mass assign guarded attributes
        $absensi->update([
            'id' => 999,
            'created_at' => now()->subYear(),
            'hadir' => false,
        ]);

        $absensi->refresh();

        // Guarded attributes should not change
        $this->assertEquals($originalId, $absensi->id);
        $this->assertEquals($originalCreatedAt->format('Y-m-d H:i:s'), $absensi->created_at->format('Y-m-d H:i:s'));

        // Non-guarded attributes should change
        $this->assertFalse($absensi->hadir);
    }

    public function test_unique_constraint_per_laporan_and_siswa(): void
    {
        // Create first attendance record
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        // Try to create duplicate (should be prevented by database constraint or business logic)
        $this->expectException(\Illuminate\Database\QueryException::class);

        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => false,
        ]);
    }

    public function test_can_update_existing_attendance(): void
    {
        $absensi = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $this->assertTrue($absensi->hadir);

        // Update attendance status
        $absensi->update(['hadir' => false]);

        $this->assertFalse($absensi->hadir);
    }

    public function test_requires_mandatory_foreign_keys(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create without required foreign keys
        Absensi::create([
            'hadir' => true,
            // Missing laporan_mengajar_id and siswa_id
        ]);
    }

    public function test_foreign_key_constraints(): void
    {
        $this->expectException(\Illuminate\Database\QueryException::class);

        // Try to create with non-existent foreign key
        Absensi::create([
            'laporan_mengajar_id' => 99999, // Non-existent ID
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);
    }

    public function test_can_scope_by_attendance_status(): void
    {
        // Create multiple attendance records
        $absensi1 = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa->id,
            'hadir' => true,
        ]);

        $siswa2 = Siswa::factory()->create(['sekolah_kodlan' => 'TEST001']);
        $absensi2 = Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $siswa2->id,
            'hadir' => false,
        ]);

        $presentCount = Absensi::where('status', 'hadir')->count();
        $absentCount = Absensi::whereIn('status', ['izin', 'sakit', 'alpha'])->count();

        $this->assertEquals(1, $presentCount);
        $this->assertEquals(1, $absentCount);
    }
}

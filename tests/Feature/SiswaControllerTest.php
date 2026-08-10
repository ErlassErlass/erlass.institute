<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sekolah;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'admin', 'is_verified' => true]);
        $this->sekolah = Sekolah::factory()->create(['kodlan' => '20218093']);
    }

    public function test_siswa_master_import_success_with_optional_fields()
    {
        // 1. CSV content without no_hp_orangtua
        $csvContent = "nama_lengkap,nisn,sekolah_kodlan,kelas\n";
        $csvContent .= "Siswa Tanpa HP,1234567800,20218093,X-IPA-1\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'import_siswa');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'Template_Import_Siswa.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->actingAs($this->user)
            ->post(route('siswa.process-import'), [
                'file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('siswa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'nama_lengkap' => 'Siswa Tanpa Hp',
            'nisn' => '1234567800',
            'sekolah_kodlan' => '20218093',
            'kelas' => 'X-IPA-1',
            'no_hp_orangtua' => null,
        ]);

        @unlink($tempFile);
    }

    public function test_siswa_master_import_success_with_full_fields()
    {
        // 2. CSV content with no_hp_orangtua
        $csvContent = "nama_lengkap,nisn,sekolah_kodlan,kelas,no_hp_orangtua\n";
        $csvContent .= "Siswa Dengan HP,1234567801,20218093,X-IPA-2,081234567890\n";

        $tempFile = tempnam(sys_get_temp_dir(), 'import_siswa');
        file_put_contents($tempFile, $csvContent);

        $uploadedFile = new \Illuminate\Http\UploadedFile(
            $tempFile,
            'Template_Import_Siswa.csv',
            'text/csv',
            null,
            true
        );

        $response = $this->actingAs($this->user)
            ->post(route('siswa.process-import'), [
                'file' => $uploadedFile,
            ]);

        $response->assertRedirect(route('siswa.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'nama_lengkap' => 'Siswa Dengan Hp',
            'nisn' => '1234567801',
            'sekolah_kodlan' => '20218093',
            'kelas' => 'X-IPA-2',
            'no_hp_orangtua' => '081234567890',
        ]);

        @unlink($tempFile);
    }
}

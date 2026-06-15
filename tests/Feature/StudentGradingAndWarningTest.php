<?php

namespace Tests\Feature;

use App\Models\Siswa;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\StudentScore;
use App\Models\Warning;
use App\Models\EkstrakurikulerSession;
use App\Services\CertificateService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StudentGradingAndWarningTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test calculation of student score averages and final weighted score.
     */
    public function test_grading_averages_and_weighted_score_calculation(): void
    {
        // Setup database records
        $sekolah = Sekolah::factory()->create();
        $siswa = Siswa::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan
        ]);
        $sales = User::factory()->create(['role' => 'sales']);
        $ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'user_id_sales' => $sales->id,
            'created_by' => $sales->id,
            'updated_by' => $sales->id,
        ]);
        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Rombel A',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 1,
            'ruangan' => 'Lab 1',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(3),
            'hari' => 'senin',
            'jam_mulai' => now()->setTime(14, 0),
            'jam_selesai' => now()->setTime(16, 0),
            'total_pertemuan' => 12,
            'frekuensi' => 'mingguan',
            'status' => 'berlangsung',
        ]);

        $score = StudentScore::create([
            'siswa_id' => $siswa->id,
            'ekstrakurikuler_id' => $ekskul->id,
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'periode' => 'Semester 1 2026',
            'nilai_kehadiran' => 80.0,
            
            // Tugas 1-4: average = 80
            'nilai_tugas_1' => 90.0,
            'nilai_tugas_2' => 80.0,
            'nilai_tugas_3' => 70.0,
            'nilai_tugas_4' => 80.0,
            
            // Sikap 1-4: average = 90
            'nilai_sikap_1' => 90.0,
            'nilai_sikap_2' => 90.0,
            'nilai_sikap_3' => 90.0,
            'nilai_sikap_4' => 90.0,
            
            // Proyek 1-4: average = 85
            'nilai_proyek_1' => 85.0,
            'nilai_proyek_2' => 85.0,
            'nilai_proyek_3' => 85.0,
            'nilai_proyek_4' => 85.0,
        ]);

        // Asserts
        $this->assertEquals(80.0, $score->nilai_tugas);
        $this->assertEquals(90.0, $score->nilai_sikap);
        $this->assertEquals(85.0, $score->nilai_proyek);
        
        // Final Score: (80 * 0.3) + (80 * 0.3) + (90 * 0.2) + (85 * 0.2) = 24 + 24 + 18 + 17 = 83.0
        $this->assertEquals(83.0, $score->nilai_akhir);
        $this->assertEquals('B+', $score->getPredikat());
        $this->assertEquals('Baik', $score->getKeterangan());
    }

    /**
     * Test certificate eligibility based on attendance threshold.
     */
    public function test_certificate_eligibility(): void
    {
        $service = new CertificateService();

        // 1. Eligible Case (attendance >= 75%)
        $eligibleScore = new StudentScore(['nilai_kehadiran' => 75.0]);
        $this->assertTrue($service->isEligible($eligibleScore));

        // 2. Not Eligible Case (attendance < 75%)
        $notEligibleScore = new StudentScore(['nilai_kehadiran' => 74.9]);
        $this->assertFalse($service->isEligible($notEligibleScore));
    }

    /**
     * Test warning detection CLI command logic.
     */
    public function test_warning_detection_command(): void
    {
        // Setup Database
        $sekolah = Sekolah::factory()->create();
        $sales = User::factory()->create(['role' => 'sales']);
        $ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'user_id_sales' => $sales->id,
            'created_by' => $sales->id,
            'updated_by' => $sales->id,
        ]);
        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Rombel B',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 1,
            'ruangan' => 'Lab 1',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(3),
            'hari' => 'senin',
            'jam_mulai' => now()->setTime(14, 0),
            'jam_selesai' => now()->setTime(16, 0),
            'total_pertemuan' => 12,
            'frekuensi' => 'mingguan',
            'status' => 'berlangsung',
        ]);

        // Fetch the session with nomor_pertemuan = 1 which was automatically generated
        $session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();

        // Update its properties for testing
        $session->update([
            'tanggal_terjadwal' => Carbon::tomorrow()->toDateString(),
            'user_id_instruktur' => null,
            'status' => 'terjadwal',
        ]);

        // Run warnings detection command
        Artisan::call('warnings:detect');

        // Verify red warning for no instructor was created
        $this->assertDatabaseHas('warnings', [
            'warning_type' => 'no_instructor',
            'sourceable_id' => $session->id,
            'sourceable_type' => EkstrakurikulerSession::class,
            'severity' => 'red',
            'status' => 'active',
        ]);

        // Assign an instructor to the session
        $instructor = User::factory()->create(['role' => 'instruktur']);
        $session->update(['user_id_instruktur' => $instructor->id]);

        // Run command again
        Artisan::call('warnings:detect');

        // Verify warning was auto-resolved
        $this->assertDatabaseHas('warnings', [
            'warning_type' => 'no_instructor',
            'sourceable_id' => $session->id,
            'status' => 'resolved',
        ]);
    }
}

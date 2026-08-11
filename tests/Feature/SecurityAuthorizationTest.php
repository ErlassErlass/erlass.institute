<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\StudentScore;
use App\Models\StudentPortfolio;
use App\Models\ReportCard;
use App\Models\Certificate;
use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\EkstrakurikulerSession;
use App\Models\LateReportRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor1;
    private User $instructor2;
    private User $sales;
    private EkstrakurikulerRombel $rombel1;
    private EkstrakurikulerRombel $rombel2;
    private Siswa $siswa1;
    private Siswa $siswa2;
    private StudentScore $score1;
    private StudentScore $score2;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // Create Users
        $this->admin = User::factory()->create(['role' => 'admin_sistem']);
        
        $this->instructor1 = User::factory()->create([
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);
        
        $this->instructor2 = User::factory()->create([
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $this->sales = User::factory()->create(['role' => 'sales']);
        $salesman = \App\Models\Salesman::factory()->create(['user_id' => $this->sales->id]);

        // Create School & Students
        $sekolah = Sekolah::factory()->create();
        $this->siswa1 = Siswa::factory()->create(['sekolah_kodlan' => $sekolah->kodlan]);
        $this->siswa2 = Siswa::factory()->create(['sekolah_kodlan' => $sekolah->kodlan]);

        // Create Extracurriculars
        $ekskul1 = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'user_id_sales' => $salesman->id,
            'created_by' => $this->sales->id,
            'updated_by' => $this->sales->id,
        ]);
        
        $ekskul2 = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'user_id_sales' => $salesman->id,
            'created_by' => $this->sales->id,
            'updated_by' => $this->sales->id,
        ]);

        // Create Rombels
        $this->rombel1 = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul1->id,
            'nama_rombel' => 'Rombel Ekskul 1',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 1,
            'ruangan' => 'Classroom 1',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(3),
            'hari' => 'senin',
            'jam_mulai' => now()->setTime(14, 0),
            'jam_selesai' => now()->setTime(16, 0),
            'total_pertemuan' => 12,
            'frekuensi' => 'mingguan',
            'status' => 'berlangsung',
            'user_id_instruktur' => $this->instructor1->id,
        ]);

        $this->rombel2 = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul2->id,
            'nama_rombel' => 'Rombel Ekskul 2',
            'nomor_rombel' => 2,
            'jumlah_siswa' => 1,
            'ruangan' => 'Classroom 2',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addMonths(3),
            'hari' => 'selasa',
            'jam_mulai' => now()->setTime(14, 0),
            'jam_selesai' => now()->setTime(16, 0),
            'total_pertemuan' => 12,
            'frekuensi' => 'mingguan',
            'status' => 'berlangsung',
            'user_id_instruktur' => $this->instructor2->id,
        ]);

        // Create Scores
        $this->score1 = StudentScore::create([
            'siswa_id' => $this->siswa1->id,
            'ekstrakurikuler_id' => $ekskul1->id,
            'ekstrakurikuler_rombel_id' => $this->rombel1->id,
            'periode' => 'Semester 1 2026',
            'nilai_kehadiran' => 100,
        ]);

        $this->score2 = StudentScore::create([
            'siswa_id' => $this->siswa2->id,
            'ekstrakurikuler_id' => $ekskul2->id,
            'ekstrakurikuler_rombel_id' => $this->rombel2->id,
            'periode' => 'Semester 1 2026',
            'nilai_kehadiran' => 100,
        ]);
    }

    /**
     * 1. Test Portfolio File Upload blocks dangerous extensions (PHP script RCE prevention)
     */
    public function test_portfolio_file_upload_blocks_dangerous_php_scripts(): void
    {
        $file = UploadedFile::fake()->create('evil_script.php', 10, 'application/x-httpd-php');

        $response = $this->actingAs($this->instructor1)->post(route('student-portfolios.store'), [
            'siswa_id' => $this->siswa1->id,
            'ekstrakurikuler_rombel_id' => $this->rombel1->id,
            'judul' => 'Tugas 1',
            'tipe_file' => 'py',
            'file_upload' => $file,
        ]);

        $response->assertSessionHasErrors(['file_upload']);
    }

    /**
     * 2. Test StudentScoreController BOLA: Instructor cannot view or modify scores of another instructor's class.
     */
    public function test_student_score_instructor_access_restricted(): void
    {
        // Instructor 1 tries to access Rombel 2 (belonging to Instructor 2)
        $responseIndex = $this->actingAs($this->instructor1)->get(route('student-scores.index', $this->rombel2->id));
        $responseIndex->assertStatus(403);

        $responseBulk = $this->actingAs($this->instructor1)->get(route('student-scores.bulk-input', $this->rombel2->id));
        $responseBulk->assertStatus(403);

        $responseStore = $this->actingAs($this->instructor1)->post(route('student-scores.store-bulk', $this->rombel2->id), [
            'scores' => [
                $this->siswa2->id => [
                    'catatan_guru' => 'Bypass attempt'
                ]
            ]
        ]);
        $responseStore->assertStatus(403);

        $responseFinalize = $this->actingAs($this->instructor1)->patch(route('student-scores.finalize', $this->rombel2->id));
        $responseFinalize->assertStatus(403);

        // Admin/Webmaster should have access
        $responseAdmin = $this->actingAs($this->admin)->get(route('student-scores.index', $this->rombel2->id));
        $responseAdmin->assertStatus(200);
    }

    /**
     * 3. Test StudentPortfolioController BOLA: Instructor cannot view or manage portfolios of another class.
     */
    public function test_student_portfolio_instructor_access_restricted(): void
    {
        // Instructor 1 tries to view Rombel 2 portfolios
        $responseIndex = $this->actingAs($this->instructor1)->get(route('student-portfolios.rombel', $this->rombel2->id));
        $responseIndex->assertStatus(403);

        // Instructor 1 tries to upload to Rombel 2
        $file = UploadedFile::fake()->create('scratch_project.pdf', 20, 'application/pdf');
        $responseStore = $this->actingAs($this->instructor1)->post(route('student-portfolios.store'), [
            'siswa_id' => $this->siswa2->id,
            'ekstrakurikuler_rombel_id' => $this->rombel2->id,
            'judul' => 'Tugas Scratch',
            'tipe_file' => 'pdf',
            'file_upload' => $file,
        ]);
        $responseStore->assertStatus(403);

        // Instructor 1 tries to delete portfolio in Rombel 2
        $portfolio = StudentPortfolio::create([
            'siswa_id' => $this->siswa2->id,
            'ekstrakurikuler_id' => $this->rombel2->ekstrakurikuler_id,
            'ekstrakurikuler_rombel_id' => $this->rombel2->id,
            'tipe_file' => 'link',
            'judul' => 'Portfolio Ekskul 2',
            'url_eksternal' => 'https://scratch.mit.edu',
            'created_by' => $this->instructor2->id,
        ]);

        $responseDelete = $this->actingAs($this->instructor1)->delete(route('student-portfolios.destroy', $portfolio->id));
        $responseDelete->assertStatus(403);

        // Admin can delete it
        $responseAdminDelete = $this->actingAs($this->admin)->delete(route('student-portfolios.destroy', $portfolio->id));
        $responseAdminDelete->assertStatus(302); // Redirect back
    }

    /**
     * 4. Test ReportCard IDOR download protection.
     */
    public function test_report_card_download_restricted(): void
    {
        $reportCard = ReportCard::create([
            'student_score_id' => $this->score2->id,
            'siswa_id' => $this->siswa2->id,
            'ekstrakurikuler_id' => $this->rombel2->ekstrakurikuler_id,
            'ekstrakurikuler_rombel_id' => $this->rombel2->id,
            'file_path' => 'uploads/reports/test_report.pdf',
            'periode' => 'Semester 1 2026',
        ]);

        // Instructor 1 cannot download student 2's report card
        $response = $this->actingAs($this->instructor1)->get(route('report-cards.download', $reportCard->id));
        $response->assertStatus(403);
    }

    /**
     * 5. Test Certificate IDOR download protection.
     */
    public function test_certificate_download_restricted(): void
    {
        $certificate = Certificate::create([
            'student_score_id' => $this->score2->id,
            'siswa_id' => $this->siswa2->id,
            'ekstrakurikuler_id' => $this->rombel2->ekstrakurikuler_id,
            'certificate_code' => 'CERT-TEST-2',
            'file_path' => 'uploads/certificates/test_cert.pdf',
            'issued_at' => now(),
        ]);

        // Instructor 1 cannot download student 2's certificate
        $response = $this->actingAs($this->instructor1)->get(route('certificates.download', $certificate->id));
        $response->assertStatus(403);
    }

    /**
     * 6. Test Payroll payslips IDOR / BOLA bypass prevention.
     */
    public function test_payroll_payslip_non_instructor_blocked(): void
    {
        $batch = PayrollBatch::create([
            'code' => 'PAY-202606',
            'periode' => '2026-06-01',
            'status' => 'processed',
        ]);

        $item = PayrollItem::create([
            'payroll_batch_id' => $batch->id,
            'user_id_instruktur' => $this->instructor1->id,
            'base_salary' => 500000,
            'status' => 'approved',
        ]);

        // Instructor 2 tries to view Instructor 1's payslip
        $responseInst2 = $this->actingAs($this->instructor2)->get(route('payroll.slip.show', $item->id));
        $responseInst2->assertStatus(403);

        // Sales user (non-instructor, non-admin) tries to view Instructor 1's payslip
        $responseSales = $this->actingAs($this->sales)->get(route('payroll.slip.show', $item->id));
        $responseSales->assertStatus(403);

        // Instructor 1 can view their own slip
        $responseInst1 = $this->actingAs($this->instructor1)->get(route('payroll.slip.show', $item->id));
        $responseInst1->assertStatus(200);
    }

    /**
     * 7. Test Absensi rekap BOLA checks.
     */
    public function test_attendance_rekap_and_export_restricted(): void
    {
        // Instructor 1 tries to access rekap for Rombel 2
        $responseRekap = $this->actingAs($this->instructor1)->get(route('rekap-absensi', [
            'rombel' => $this->rombel2->nama_rombel,
        ]));
        $responseRekap->assertStatus(403);

        // Instructor 1 tries to export rekap for Rombel 2
        $responseExport = $this->actingAs($this->instructor1)->get(route('rekap-absensi.export', [
            'rombel' => $this->rombel2->nama_rombel,
        ]));
        $responseExport->assertStatus(403);
    }

    /**
     * 8. Test LateReportRequest BOLA checks.
     */
    public function test_late_report_request_restricted(): void
    {
        // Fetch or update the automatically generated session (nomor_pertemuan = 2) for Rombel 2
        $session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel2->id)
            ->where('nomor_pertemuan', 2)
            ->first();

        if (!$session) {
            $session = EkstrakurikulerSession::create([
                'ekstrakurikuler_id' => $this->rombel2->ekstrakurikuler_id,
                'ekstrakurikuler_rombel_id' => $this->rombel2->id,
                'nomor_pertemuan' => 99,
                'tanggal_terjadwal' => now()->subDays(2),
                'jam_mulai_terjadwal' => now()->setTime(14, 0),
                'jam_selesai_terjadwal' => now()->setTime(16, 0),
                'status' => 'terjadwal',
                'user_id_instruktur' => $this->instructor2->id,
            ]);
        } else {
            $session->update([
                'status' => 'terjadwal',
                'user_id_instruktur' => $this->instructor2->id,
            ]);
        }

        // Instructor 1 tries to request late report for Instructor 2's session
        $response = $this->actingAs($this->instructor1)->post(route('sessions.late-report-request.store', $session->id), [
            'reason' => 'Permohonan terlambat dari instruktur lain.',
        ]);
        
        $response->assertStatus(302); // Redirect back with error
        $response->assertSessionHas('error', 'Akses ditolak. Anda bukan instruktur atau asisten untuk sesi ini.');
    }
}

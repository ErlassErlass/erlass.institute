<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\InstructorProfile;
use App\Models\LaporanMengajar;
use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\SalaryRate;
use App\Models\User;
use App\Models\Sekolah;
use App\Services\PayrollCalculatorService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Prevent database event issues during testing
        Carbon::setTestNow(Carbon::create(2026, 6, 15, 10, 0, 0));
    }

    public function test_admin_can_manage_salary_rates()
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'webmaster',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        // List
        $response = $this->actingAs($admin)->get(route('admin.salary-rates.index'));
        $response->assertStatus(200);

        // Create
        $response = $this->actingAs($admin)->post(route('admin.salary-rates.store'), [
            'level' => 'junior',
            'base_rate' => 120000.00,
            'product_category' => 'Robotik',
            'product_bonus' => 30000.00,
        ]);
        $response->assertRedirect(route('admin.salary-rates.index'));
        $this->assertDatabaseHas('salary_rates', [
            'level' => 'junior',
            'base_rate' => 120000.00,
            'product_category' => 'Robotik',
            'product_bonus' => 30000.00,
        ]);
    }

    public function test_instructor_cannot_manage_salary_rates()
    {
        $instructor = User::create([
            'nama_lengkap' => 'Instructor Test',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $response = $this->actingAs($instructor)->get(route('admin.salary-rates.index'));
        $response->assertStatus(403);

        $response = $this->actingAs($instructor)->post(route('admin.salary-rates.store'), [
            'level' => 'junior',
            'base_rate' => 120000.00,
        ]);
        $response->assertStatus(403);
    }

    public function test_payroll_calculator_service_calculates_correct_fee_and_punctuality()
    {
        $instructor = User::create([
            'nama_lengkap' => 'Instructor Test',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        // Create instructor profile with level
        InstructorProfile::create([
            'user_id' => $instructor->id,
            'level' => 'madya',
        ]);

        // Create base salary rate for madya
        SalaryRate::create([
            'level' => 'madya',
            'base_rate' => 150000.00,
            'product_bonus' => 0.00,
        ]);

        // Create product category bonus rate
        SalaryRate::create([
            'level' => 'madya',
            'base_rate' => 150000.00,
            'product_category' => 'Robotik',
            'product_bonus' => 50000.00,
        ]);

        // Create school, extracurricular, and rombel
        $sekolah = Sekolah::factory()->create([
            'kodlan' => 'SCH001',
            'namasekolah' => 'Test School',
            'kota' => 'Jakarta',
        ]);

        $ekskul = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Robotik Explorer',
            'sekolah_kodlan' => 'SCH001',
            'status' => 'aktif',
            'jenis_pembayaran' => 'per_siswa_bulan',
            'total_pertemuan' => 12,
            'jarak_km' => 12.50,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Robotics A',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 15,
            'ruangan' => 'Lab 1',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'senin',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);

        // Case 1: On time checkin (no penalty)
        $session = $rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session->update([
            'tanggal_pelaksanaan' => '2026-06-15',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $service = new PayrollCalculatorService();
        $calc = $service->calculateSessionFee($session);

        $this->assertEquals(150000.00, $calc['base_rate']);
        $this->assertEquals(50000.00, $calc['product_bonus']);
        $this->assertEquals(200000.00, $calc['calculated_fee']);
        $this->assertEquals('on_time', $calc['actual_checkin_status']);
        $this->assertEquals(0.00, $calc['actual_checkin_penalty']);
        $this->assertEquals(200000.00, $calc['net_fee']);
        $this->assertEquals(16250.00, $calc['transport_fee']); // (12.5 * 350 * 2) + 7500 = 16250

        // Case 2: Late checkin (penalty applied)
        $sessionLate = $rombel->sessions()->where('nomor_pertemuan', 2)->first();
        $sessionLate->update([
            'tanggal_pelaksanaan' => '2026-06-22',
            'jam_mulai_aktual' => '14:16', // 16 mins late
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $calcLate = $service->calculateSessionFee($sessionLate);
        $this->assertEquals('penalty', $calcLate['actual_checkin_status']);
        $this->assertEquals(25000.00, $calcLate['actual_checkin_penalty']);
        $this->assertEquals(175000.00, $calcLate['net_fee']);
    }

    public function test_monthly_payroll_generation_and_transitions()
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'webmaster',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $instructor = User::create([
            'nama_lengkap' => 'Instructor Test',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        InstructorProfile::create([
            'user_id' => $instructor->id,
            'level' => 'junior',
        ]);

        SalaryRate::create([
            'level' => 'junior',
            'base_rate' => 100000.00,
        ]);

        $sekolah = Sekolah::factory()->create([
            'kodlan' => 'SCH001',
            'namasekolah' => 'Test School',
            'kota' => 'Jakarta',
        ]);

        $ekskul = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Coding Scratch',
            'sekolah_kodlan' => 'SCH001',
            'status' => 'aktif',
            'jenis_pembayaran' => 'per_siswa_bulan',
            'total_pertemuan' => 12,
            'jarak_km' => 5.00,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Scratch Junior',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 15,
            'ruangan' => 'Lab 1',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'rabu',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);

        $session = $rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session->update([
            'tanggal_pelaksanaan' => '2026-06-03',
            'jam_mulai_aktual' => '10:00',
            'jam_selesai_aktual' => '12:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        // Session must have LaporanMengajar to be compiled
        LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session->id,
            'user_id_instruktur' => $instructor->id,
            'pertemuan_ke' => 1,
            'rombel' => $rombel->nama_rombel,
            'jadwal_mengajar' => '2026-06-03',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'kategori_pengajaran' => 'ekstrakurikuler',
            'materi_pengajaran' => 'Scratch Intro',
            'sekolah_kodlan' => 'SCH001',
            'jumlah_siswa_hadir' => 15,
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
        ]);

        // 1. Create batch via web interface
        $response = $this->actingAs($admin)->post(route('admin.payroll.batches.store'), [
            'month' => '2026-06',
            'notes' => 'Test Batch June',
        ]);

        $batch = PayrollBatch::first();
        $this->assertNotNull($batch);
        $this->assertEquals('draft', $batch->status);

        $response->assertRedirect(route('admin.payroll.batches.show', $batch->id));

        // Assert session is now locked (processing) and payroll item is created
        $session->refresh();
        $this->assertEquals('processing', $session->payment_status);
        $this->assertNotNull($session->payroll_item_id);

        $payrollItem = PayrollItem::first();
        $this->assertNotNull($payrollItem);
        $this->assertEquals($batch->id, $payrollItem->payroll_batch_id);
        $this->assertEquals($instructor->id, $payrollItem->user_id_instruktur);
        $this->assertEquals(7500.00, $payrollItem->total_transport_fee);
        $this->assertEquals(157500.00, $payrollItem->total_gross_salary);
        $this->assertEquals(3938.00, $payrollItem->tax_amount);
        $this->assertEquals(153563.00, $payrollItem->net_salary);

        // 2. Process batch
        $response = $this->actingAs($admin)->post(route('admin.payroll.batches.process', $batch->id));
        $batch->refresh();
        $this->assertEquals('processed', $batch->status);

        // 3. Pay batch
        $response = $this->actingAs($admin)->post(route('admin.payroll.batches.pay', $batch->id));
        $batch->refresh();
        $session->refresh();
        $payrollItem->refresh();

        $this->assertEquals('paid', $batch->status);
        $this->assertEquals('paid', $session->payment_status);
        $this->assertEquals('paid', $payrollItem->status);
    }

    public function test_payroll_calculator_transport_fee_priorities()
    {
        $instructor = User::create([
            'nama_lengkap' => 'Instructor Test',
            'email' => 'instructor@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        InstructorProfile::create([
            'user_id' => $instructor->id,
            'level' => 'junior',
        ]);

        SalaryRate::create([
            'level' => 'junior',
            'base_rate' => 100000.00,
        ]);

        $sekolah = Sekolah::factory()->create([
            'kodlan' => 'SCH002',
            'namasekolah' => 'Priority Test School',
            'kota' => 'Jakarta',
            'kustom_transport_fee' => 45000.00,
        ]);

        $ekskul = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Coding Scratch',
            'sekolah_kodlan' => 'SCH002',
            'status' => 'aktif',
            'jenis_pembayaran' => 'per_siswa_bulan',
            'total_pertemuan' => 12,
            'jarak_km' => null, // Jarak null
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Scratch Priority',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 0,
            'ruangan' => 'Lab 1',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'rabu',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);

        $session = $rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session->update([
            'tanggal_pelaksanaan' => '2026-06-03',
            'jam_mulai_aktual' => '10:00',
            'jam_selesai_aktual' => '12:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $service = new PayrollCalculatorService();

        // Priority 1: sekolah has kustom_transport_fee = 45000, ekskul jarak_km is null
        $calc1 = $service->calculateSessionFee($session);
        $this->assertEquals(90000.00, $calc1['transport_fee']);

        // Priority 2: ekskul jarak_km is defined (15 km >= 10 km) -> (15 * 350 * 2) + 7500 = 18000
        $ekskul->update(['jarak_km' => 15.00]);
        $session->refresh();
        $calc2 = $service->calculateSessionFee($session);
        $this->assertEquals(18000.00, $calc2['transport_fee']);

        // Priority 2 (Under 10 KM): ekskul jarak_km is defined (< 10 km) -> 7500.00 (Sewa Kendaraan Saja)
        $ekskul->update(['jarak_km' => 5.00]);
        $session->refresh();
        $calc2Min = $service->calculateSessionFee($session);
        $this->assertEquals(7500.00, $calc2Min['transport_fee']);

        // Priority 3 (Fallback): both jarak_km is 0 and kustom_transport_fee is null -> 0.00
        $ekskul->update(['jarak_km' => 0.00]);
        $sekolah->update(['kustom_transport_fee' => null]);
        $session->refresh();
        $calc3 = $service->calculateSessionFee($session);
        $this->assertEquals(0.00, $calc3['transport_fee']);
    }

    public function test_assistant_fee_is_flat_100k_and_no_transport()
    {
        $assistant = User::create([
            'nama_lengkap' => 'Assistant Test',
            'email' => 'assistant@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1995-05-05',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081298765432',
        ]);

        $sekolah = Sekolah::factory()->create(['kodlan' => 'SCH003', 'namasekolah' => 'Sekolah Asisten']);
        $ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => 'SCH003',
            'jarak_km' => 20.00,
        ]);
        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Robotics Big Class',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 28,
            'ruangan' => 'Lab 2',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'kamis',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);

        $session = $rombel->sessions()->first();
        $session->update([
            'tanggal_pelaksanaan' => '2026-06-04',
            'jam_mulai_aktual' => '13:00',
            'jam_selesai_aktual' => '15:00',
            'status' => 'selesai',
            'user_id_asisten' => $assistant->id,
        ]);

        $service = new PayrollCalculatorService();
        $calcAsisten = $service->calculateSessionFee($session, 'asisten');

        $this->assertEquals(100000.00, $calcAsisten['base_rate']);
        $this->assertEquals(0.00, $calcAsisten['transport_fee']);
        $this->assertEquals(0.00, $calcAsisten['actual_checkin_penalty']);
        $this->assertEquals(100000.00, $calcAsisten['net_fee']);
    }

    public function test_dual_role_session_calculates_and_generates_pivot_records()
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Test',
            'email' => 'admin_dual@test.com',
            'password' => bcrypt('password'),
            'role' => 'webmaster',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $leadTeacher = User::create([
            'nama_lengkap' => 'Guru Utama',
            'email' => 'guru.utama@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567891',
        ]);

        $assistantTeacher = User::create([
            'nama_lengkap' => 'Guru Asisten',
            'email' => 'guru.asisten@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1992-02-02',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567892',
        ]);

        $sekolah = Sekolah::factory()->create(['kodlan' => 'SCH004', 'namasekolah' => 'Sekolah Dual']);
        $ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => 'SCH004',
            'jarak_km' => 10.00,
        ]);
        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Big Class Dual',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 26,
            'ruangan' => 'Lab 3',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'jumat',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);

        $session = $rombel->sessions()->first();
        $session->update([
            'tanggal_pelaksanaan' => '2026-06-05',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $leadTeacher->id,
            'user_id_asisten' => $assistantTeacher->id,
        ]);

        LaporanMengajar::create([
            'ekstrakurikuler_session_id' => $session->id,
            'user_id_instruktur' => $leadTeacher->id,
            'pertemuan_ke' => 1,
            'rombel' => $rombel->nama_rombel,
            'jadwal_mengajar' => '2026-06-05',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'kategori_pengajaran' => 'ekstrakurikuler',
            'materi_pengajaran' => 'Dual Session Test',
            'sekolah_kodlan' => 'SCH004',
            'jumlah_siswa_hadir' => 26,
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
        ]);

        $batch = PayrollBatch::create([
            'code' => 'PAY-202606-TEST',
            'periode' => '2026-06-01',
            'status' => 'draft',
        ]);

        $service = new PayrollCalculatorService();
        $count = $service->generateMonthlyPayroll($batch);

        $this->assertEquals(2, $count); // Created items for both lead & assistant

        $leadItem = PayrollItem::where('payroll_batch_id', $batch->id)
            ->where('user_id_instruktur', $leadTeacher->id)
            ->first();
        $this->assertNotNull($leadItem);
        $this->assertEquals(1, $leadItem->total_sessions_utama);
        $this->assertEquals(0, $leadItem->total_sessions_asisten);
        $this->assertEquals(150000.00, $leadItem->total_base_fee);
        $this->assertEquals(14500.00, $leadItem->total_transport_fee); // (10 * 350 * 2) + 7500 = 14500
        $this->assertEquals(164500.00, $leadItem->total_gross_salary);
        $this->assertEquals(4113.00, $leadItem->tax_amount); // round(164500 * 0.025)
        $this->assertEquals(160388.00, $leadItem->net_salary); // round(164500 * 0.975)

        $asistenItem = PayrollItem::where('payroll_batch_id', $batch->id)
            ->where('user_id_instruktur', $assistantTeacher->id)
            ->first();
        $this->assertNotNull($asistenItem);
        $this->assertEquals(0, $asistenItem->total_sessions_utama);
        $this->assertEquals(1, $asistenItem->total_sessions_asisten);
        $this->assertEquals(0.00, $asistenItem->total_base_fee);
        $this->assertEquals(100000.00, $asistenItem->total_asisten_fee);
        $this->assertEquals(0.00, $asistenItem->total_transport_fee);
        $this->assertEquals(100000.00, $asistenItem->total_gross_salary);
        $this->assertEquals(2500.00, $asistenItem->tax_amount); // round(100000 * 0.025)
        $this->assertEquals(97500.00, $asistenItem->net_salary); // round(100000 * 0.975)

        // Check pivot records in payroll_item_session
        $this->assertDatabaseHas('payroll_item_session', [
            'payroll_item_id' => $leadItem->id,
            'ekstrakurikuler_session_id' => $session->id,
            'role' => 'utama',
            'base_fee' => 150000.00,
            'transport_fee' => 14500.00,
        ]);

        $this->assertDatabaseHas('payroll_item_session', [
            'payroll_item_id' => $asistenItem->id,
            'ekstrakurikuler_session_id' => $session->id,
            'role' => 'asisten',
            'base_fee' => 100000.00,
            'transport_fee' => 0.00,
        ]);
    }

    public function test_export_pdf_view_renders_with_session_and_instructor_details()
    {
        $admin = User::create([
            'nama_lengkap' => 'Admin Payroll',
            'email' => 'admin_payroll@test.com',
            'password' => bcrypt('password'),
            'role' => 'webmaster',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $instructor = User::create([
            'nama_lengkap' => 'Instructor Utama Test',
            'email' => 'utama@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $batch = PayrollBatch::create([
            'code' => 'PAY-TEST-01',
            'periode' => '2026-06-01',
            'status' => 'draft',
        ]);

        $item = PayrollItem::create([
            'payroll_batch_id' => $batch->id,
            'user_id_instruktur' => $instructor->id,
            'total_sessions' => 1,
            'total_sessions_utama' => 1,
            'total_sessions_asisten' => 0,
            'total_base_fee' => 150000,
            'total_asisten_fee' => 0,
            'total_transport_fee' => 10000,
            'total_gross_salary' => 160000,
            'tax_rate' => 0.025,
            'tax_amount' => 4000,
            'net_salary' => 156000,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.payroll.batches.export-pdf', $batch->id));
        $response->assertStatus(200);
        $response->assertSee('Honor Utama');
        $response->assertSee('Honor Asisten');
        $response->assertSee('Pajak (2.5%)');
        $response->assertSee('AUDIT RINCIAN PER SESI MENGAJAR');
    }
}

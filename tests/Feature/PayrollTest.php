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
        $this->assertEquals(16500.00, $calc['transport_fee']); // (12.5 * 350 * 2) + 7500 = 16250 -> ceil to 500 = 16500

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
        $this->assertEquals(0.00, $calcLate['actual_checkin_penalty']); // Potongan keterlambatan ditiadakan
        $this->assertEquals(200000.00, $calcLate['net_fee']);

        // Case 3: Student count < 8 (including 0 students) in regular ekskul session still paid minimum 75k
        $rombelSmall = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Small Class',
            'nomor_rombel' => 99,
            'jumlah_siswa' => 0,
            'ruangan' => 'Lab',
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'senin',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 1,
            'status' => 'berlangsung',
        ]);
        $sessionSmall = $rombelSmall->sessions()->first();
        $sessionSmall->update([
            'tanggal_pelaksanaan' => '2026-06-22',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);
        $calcSmall = $service->calculateSessionFee($sessionSmall);
        $this->assertEquals(75000.00, $calcSmall['base_rate']); // Minimum 75k even if 0 students

        // Case 4: Cancelled or Libur session receives 0 fee
        $sessionSmall->update(['status' => 'dibatalkan']);
        $calcCancelled = $service->calculateSessionFee($sessionSmall);
        $this->assertEquals(0.00, $calcCancelled['base_rate']);
        $this->assertEquals(0.00, $calcCancelled['transport_fee']);
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

    public function test_assistant_fee_is_flat_100k_and_has_transport()
    {
        $assistant = User::factory()->create(['role' => 'instruktur']);
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
        $this->assertEquals(21500.00, $calcAsisten['transport_fee']);
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
        $this->assertEquals(14500.00, $asistenItem->total_transport_fee);
        $this->assertEquals(114500.00, $asistenItem->total_gross_salary);
        $this->assertEquals(2863.00, $asistenItem->tax_amount); // round(114500 * 0.025)
        $this->assertEquals(111638.00, $asistenItem->net_salary); // round(114500 * 0.975)

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
            'transport_fee' => 14500.00,
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
        $response->assertSee('Honor Instruktur');
        $response->assertSee('Honor Asisten');
        $response->assertSee('Pajak (2.5%)');
        $response->assertSee('AUDIT RINCIAN PER SESI MENGAJAR');
    }

    public function test_al_ikhlas_and_semester_tahunan_schools_have_flat_150k_base_rate_for_regular_sessions()
    {
        $service = new PayrollCalculatorService();

        $instructor = User::create([
            'nama_lengkap' => 'Instructor Flat 150k Test',
            'email' => 'flat150k@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'SMA',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        // 1. SDI AL IKHLAS (Kodlan 20105810) - Regular session with only 3 students (< 8)
        $sekolahIkhlas = Sekolah::factory()->create([
            'kodlan' => '20105810',
            'namasekolah' => 'SDI AL IKHLAS',
            'skema_tagihan' => 'per_4_pertemuan',
            'jarak_km' => 5.0,
        ]);
        $ekskulIkhlas = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Ekskul Coding Scratch',
            'sekolah_kodlan' => $sekolahIkhlas->kodlan,
            'status' => 'aktif',
            'total_pertemuan' => 12,
            'jarak_km' => 5.0,
        ]);
        $rombelIkhlas = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskulIkhlas->id,
            'nama_rombel' => 'Scratch A',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 3, // < 8 students
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'senin',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);
        $sessionIkhlas = $rombelIkhlas->sessions()->where('nomor_pertemuan', 1)->first();
        $sessionIkhlas->update([
            'tanggal_pelaksanaan' => '2026-06-15',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $calcIkhlas = $service->calculateSessionFee($sessionIkhlas, 'utama');
        $this->assertEquals(150000.00, $calcIkhlas['base_rate'], 'SDI Al Ikhlas regular session must be flat Rp 150.000 even with < 8 students');
        $this->assertEquals(7500.00, $calcIkhlas['transport_fee'], 'Transport fee must still be calculated for SDI Al Ikhlas');

        // Asisten in SDI Al Ikhlas must remain 100k
        $calcIkhlasAsisten = $service->calculateSessionFee($sessionIkhlas, 'asisten');
        $this->assertEquals(100000.00, $calcIkhlasAsisten['base_rate'], 'Assistant in SDI Al Ikhlas must be flat Rp 100.000');

        // 2. Semester School - Regular session with 5 students (< 8)
        $sekolahSemester = Sekolah::factory()->create([
            'kodlan' => 'SCH_SEM_01',
            'namasekolah' => 'Sekolah Semester Test',
            'skema_tagihan' => 'semester',
            'jarak_km' => 12.0,
        ]);
        $ekskulSemester = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Ekskul Robotik Explorer',
            'sekolah_kodlan' => $sekolahSemester->kodlan,
            'status' => 'aktif',
            'total_pertemuan' => 12,
        ]);
        $rombelSemester = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskulSemester->id,
            'nama_rombel' => 'Robotics Sem',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 5,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'selasa',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);
        $sessionSemester = $rombelSemester->sessions()->where('nomor_pertemuan', 1)->first();
        $sessionSemester->update([
            'tanggal_pelaksanaan' => '2026-06-16',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $calcSemester = $service->calculateSessionFee($sessionSemester, 'utama');
        $this->assertEquals(150000.00, $calcSemester['base_rate'], 'Semester school regular session must be flat Rp 150.000');

        // 3. Tahunan School - Regular session with 0 students
        $sekolahTahunan = Sekolah::factory()->create([
            'kodlan' => 'SCH_TAHUN_01',
            'namasekolah' => 'Sekolah Tahunan Test',
            'skema_tagihan' => 'tahunan',
            'jarak_km' => 8.0,
        ]);
        $ekskulTahunan = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Ekskul Erboblox',
            'sekolah_kodlan' => $sekolahTahunan->kodlan,
            'status' => 'aktif',
            'total_pertemuan' => 12,
        ]);
        $rombelTahunan = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskulTahunan->id,
            'nama_rombel' => 'Erboblox Tahunan',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 0,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'rabu',
            'jam_mulai' => '14:00',
            'jam_selesai' => '16:00',
            'total_pertemuan' => 12,
            'status' => 'berlangsung',
        ]);
        $sessionTahunan = $rombelTahunan->sessions()->where('nomor_pertemuan', 1)->first();
        $sessionTahunan->update([
            'tanggal_pelaksanaan' => '2026-06-17',
            'jam_mulai_aktual' => '14:00',
            'jam_selesai_aktual' => '16:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
        ]);

        $calcTahunan = $service->calculateSessionFee($sessionTahunan, 'utama');
        $this->assertEquals(150000.00, $calcTahunan['base_rate'], 'Tahunan school regular session must be flat Rp 150.000');

        // 4. Special Session (Free Trial) in SDI Al Ikhlas must keep special trial rate
        $ekskulTrial = Ekstrakurikuler::factory()->create([
            'kategori_program' => 'Free Trial Class',
            'sekolah_kodlan' => $sekolahIkhlas->kodlan,
            'status' => 'aktif',
            'total_pertemuan' => 1,
        ]);
        $rombelTrial = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskulTrial->id,
            'nama_rombel' => 'Trial Class Rombel',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 4,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'kamis',
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'total_pertemuan' => 1,
            'status' => 'berlangsung',
        ]);
        $sessionTrial = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $ekskulTrial->id,
            'ekstrakurikuler_rombel_id' => $rombelTrial->id,
            'nomor_pertemuan' => 1,
            'tanggal_terjadwal' => '2026-06-18',
            'tanggal_pelaksanaan' => '2026-06-18',
            'jam_mulai_terjadwal' => '10:00',
            'jam_selesai_terjadwal' => '11:00',
            'jam_mulai_aktual' => '10:00',
            'jam_selesai_aktual' => '11:00',
            'status' => 'selesai',
            'user_id_instruktur' => $instructor->id,
            'topik_materi' => 'Free Trial Class Robotik',
        ]);
        $calcTrial = $service->calculateSessionFee($sessionTrial, 'utama');
        $this->assertEquals(100000.00, $calcTrial['base_rate'], 'Free trial session is flat Rp 100.000 regardless of student count');
    }

    public function test_recalculate_batch_absorbs_new_unpaid_sessions_and_protects_existing_sessions()
    {
        $service = app(PayrollCalculatorService::class);

        $school = Sekolah::factory()->create([
            'kodlan' => 'SCH-RECALC-01',
            'namasekolah' => 'SD Recalc Test',
            'jarak_km' => 10,
        ]);

        $ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $school->kodlan,
            'kategori_program' => 'Ekskul Coding Scratch',
            'jenis_program' => 'ekstrakurikuler',
            'status' => 'aktif',
            'total_pertemuan' => 10,
        ]);

        $rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nama_rombel' => 'Rombel A',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 15,
            'tanggal_mulai' => '2026-06-01',
            'tanggal_selesai' => '2026-12-01',
            'hari' => 'senin',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'total_pertemuan' => 10,
            'status' => 'berlangsung',
        ]);

        $instructor1 = User::create([
            'nama_lengkap' => 'Instruktur Eksisting',
            'email' => 'ins1@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1995-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567891',
        ]);

        $instructor2 = User::create([
            'nama_lengkap' => 'Instruktur Baru Menyusul',
            'email' => 'ins2@test.com',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1996-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567892',
        ]);

        $batch = PayrollBatch::create([
            'code' => 'PAY-202607-TEST',
            'periode' => '2026-07-01',
            'status' => 'draft',
        ]);

        // Sesi 1: Sudah ada di batch untuk Instruktur 1, tapi laporan dibuat late (setelah cutoff 10 Juli)
        $session1 = $rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session1->update([
            'tanggal_terjadwal' => '2026-06-20',
            'tanggal_pelaksanaan' => '2026-06-20',
            'jam_mulai_terjadwal' => '13:00',
            'jam_selesai_terjadwal' => '14:30',
            'jam_mulai_aktual' => '13:00',
            'jam_selesai_aktual' => '14:30',
            'status' => 'selesai',
            'payment_status' => 'processing',
            'user_id_instruktur' => $instructor1->id,
            'calculated_fee' => 100000,
            'transport_fee' => 11000,
        ]);
        $report1 = LaporanMengajar::create([
            'user_id_instruktur' => $instructor1->id,
            'ekstrakurikuler_session_id' => $session1->id,
            'sekolah_kodlan' => $school->kodlan,
            'pertemuan_ke' => 1,
            'rombel' => 1,
            'jadwal_mengajar' => '2026-06-20 13:00:00',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Ekskul Coding Scratch',
            'materi_pengajaran' => 'Test',
            'jumlah_siswa_hadir' => 15,
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'created_at' => '2026-07-15 10:00:00', // Submitted post-cutoff!
        ]);

        $slip1 = PayrollItem::create([
            'payroll_batch_id' => $batch->id,
            'user_id_instruktur' => $instructor1->id,
            'total_sessions' => 1,
            'total_sessions_utama' => 1,
            'total_base_fee' => 100000,
            'total_transport_fee' => 11000,
            'total_gross_salary' => 111000,
            'tax_amount' => 2775,
            'net_salary' => 108225,
            'status' => 'pending',
        ]);
        \App\Models\PayrollItemSession::create([
            'payroll_item_id' => $slip1->id,
            'ekstrakurikuler_session_id' => $session1->id,
            'user_id' => $instructor1->id,
            'role' => 'utama',
            'base_fee' => 100000,
            'transport_fee' => 11000,
            'net_fee' => 111000,
        ]);
        $session1->update(['payroll_item_id' => $slip1->id]);

        // Sesi 2: Baru selesai untuk Instruktur 2, masih 'unpaid' dalam periode cutoff
        $session2 = $rombel->sessions()->where('nomor_pertemuan', 2)->first();
        $session2->update([
            'tanggal_terjadwal' => '2026-06-27',
            'tanggal_pelaksanaan' => '2026-06-27',
            'jam_mulai_terjadwal' => '13:00',
            'jam_selesai_terjadwal' => '14:30',
            'jam_mulai_aktual' => '13:00',
            'jam_selesai_aktual' => '14:30',
            'status' => 'selesai',
            'payment_status' => 'unpaid',
            'user_id_instruktur' => $instructor2->id,
        ]);
        $report2 = LaporanMengajar::create([
            'user_id_instruktur' => $instructor2->id,
            'ekstrakurikuler_session_id' => $session2->id,
            'sekolah_kodlan' => $school->kodlan,
            'pertemuan_ke' => 2,
            'rombel' => 1,
            'jadwal_mengajar' => '2026-06-27 13:00:00',
            'jam_mulai' => '13:00',
            'jam_selesai' => '14:30',
            'kategori_pengajaran' => 'Ekskul Coding Scratch',
            'materi_pengajaran' => 'Test Sesi Baru',
            'jumlah_siswa_hadir' => 15,
            'keaktifan' => 'aktif',
            'pemahaman_materi' => 'paham',
            'refleksi_siswa' => '-',
            'refleksi_capaian' => '-',
            'created_at' => '2026-06-28 10:00:00',
        ]);

        // Jalankan Recalculate Batch
        $res = $service->recalculateBatch($batch);

        // Verifikasi
        $this->assertEquals(1, $res['newly_absorbed_sessions'], 'Harus menyerap 1 sesi baru yang unpaid');
        $this->assertEquals(2, $res['items_count'], 'Batch harus memiliki 2 instruktur');

        // Pastikan Sesi 1 Instruktur 1 TIDAK DI-EJECT (tetap ada)
        $this->assertTrue(
            \App\Models\PayrollItemSession::where('payroll_item_id', $slip1->id)
                ->where('ekstrakurikuler_session_id', $session1->id)
                ->exists(),
            'Sesi 1 yang laporannya disubmit post-cutoff TIDAK boleh di-eject saat recalculate'
        );

        // Pastikan Sesi 2 Instruktur 2 otomatis masuk ke batch
        $slip2 = PayrollItem::where('payroll_batch_id', $batch->id)
            ->where('user_id_instruktur', $instructor2->id)
            ->first();
        $this->assertNotNull($slip2, 'Slip untuk Instruktur 2 otomatis dibuat');
        $this->assertTrue(
            \App\Models\PayrollItemSession::where('payroll_item_id', $slip2->id)
                ->where('ekstrakurikuler_session_id', $session2->id)
                ->exists(),
            'Sesi 2 harus tersambung ke slip Instruktur 2'
        );
        $this->assertEquals('processing', $session2->fresh()->payment_status);
    }
}

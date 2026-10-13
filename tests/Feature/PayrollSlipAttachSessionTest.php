<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\LaporanMengajar;
use App\Models\PayrollBatch;
use App\Models\PayrollItem;
use App\Models\PayrollItemSession;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollSlipAttachSessionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $instructor;
    private PayrollBatch $batch;
    private PayrollItem $payrollItem;
    private Sekolah $sekolah;
    private Ekstrakurikuler $ekskul;
    private EkstrakurikulerRombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'nama_lengkap' => 'Admin Payroll Test',
            'role' => 'admin_sistem',
            'email' => 'admin.payroll@erlass.institute',
        ]);

        $this->instructor = User::factory()->create([
            'nama_lengkap' => 'Instruktur Budi Test',
            'role' => 'instruktur',
            'email' => 'budi.instruktur@erlass.institute',
        ]);

        $this->batch = PayrollBatch::create([
            'code' => 'PAY-202610-TEST',
            'periode' => '2026-10-01',
            'status' => 'draft',
        ]);

        $this->payrollItem = PayrollItem::create([
            'payroll_batch_id' => $this->batch->id,
            'user_id_instruktur' => $this->instructor->id,
            'total_sessions' => 0,
            'total_sessions_utama' => 0,
            'total_sessions_asisten' => 0,
            'total_base_fee' => 0,
            'total_asisten_fee' => 0,
            'total_product_bonus' => 0,
            'total_penalty' => 0,
            'total_transport_fee' => 0,
            'total_gross_salary' => 0,
            'tax_rate' => 2.5,
            'tax_amount' => 0,
            'net_salary' => 0,
            'status' => 'pending',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'namasekolah' => 'SD Bintang Harapan',
            'kodlan' => '999001',
            'jarak_km' => 5.0,
        ]);

        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'kategori_program' => 'Coding',
        ]);

        $this->rombel = EkstrakurikulerRombel::factory()->create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Kelas Coding A',
            'jumlah_siswa' => 10,
            'user_id_instruktur' => $this->instructor->id,
        ]);
    }

    public function test_admin_can_view_slip_with_eligible_sessions(): void
    {
        // Ambil sesi nomor 1 dari rombel dan update ke status selesai, unpaid, tanggal sebelum cutoff tgl 10
        $session = $this->rombel->sessions()->where('nomor_pertemuan', 1)->first();
        $session->update([
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_pelaksanaan' => '2026-10-05',
            'tanggal_terjadwal' => '2026-10-05',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'payment_status' => 'unpaid',
        ]);

        LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $session->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Kelas Coding A',
            'pertemuan_ke' => $session->nomor_pertemuan,
            'jadwal_mengajar' => $session->tanggal_pelaksanaan,
            'jumlah_siswa_hadir' => 10,
            'materi_pengajaran' => 'Pengenalan Algoritma',
        ]);

        $response = $this->actingAs($this->admin)->get(route('payroll.slip.show', $this->payrollItem->id));
        $response->assertOk();
        $response->assertSee('Tambah Sesi ke Slip');
        $response->assertSee('Pertemuan Ke-1 (ID #' . $session->id . ')');
    }

    public function test_admin_can_attach_session_to_slip_and_recalculates_totals(): void
    {
        $session = $this->rombel->sessions()->where('nomor_pertemuan', 2)->first();
        $session->update([
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_pelaksanaan' => '2026-10-08',
            'tanggal_terjadwal' => '2026-10-08',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'payment_status' => 'unpaid',
        ]);

        LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $session->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Kelas Coding A',
            'pertemuan_ke' => $session->nomor_pertemuan,
            'jadwal_mengajar' => $session->tanggal_pelaksanaan,
            'jumlah_siswa_hadir' => 10,
            'materi_pengajaran' => 'Looping & Variable',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.payroll.items.attach-session', $this->payrollItem->id),
            ['session_id' => $session->id]
        );

        $response->assertRedirect(route('payroll.slip.show', $this->payrollItem->id));
        $response->assertSessionHas('success');

        // Pastikan relasi pivot terbentuk
        $this->assertDatabaseHas('payroll_item_session', [
            'payroll_item_id' => $this->payrollItem->id,
            'ekstrakurikuler_session_id' => $session->id,
            'user_id' => $this->instructor->id,
        ]);

        // Status sesi berubah jadi processing
        $this->assertEquals('processing', $session->fresh()->payment_status);

        // Payroll item total sesi bertambah jadi 1 dan net_salary > 0
        $this->payrollItem->refresh();
        $this->assertEquals(1, $this->payrollItem->total_sessions);
        $this->assertGreaterThan(0, (float) $this->payrollItem->net_salary);
    }

    public function test_cannot_attach_session_exceeding_cutoff_date(): void
    {
        // Sesi pada 15 Oktober 2026 (lewat batas cutoff tanggal 10 Oktober 2026)
        $sessionPostCutoff = $this->rombel->sessions()->where('nomor_pertemuan', 3)->first();
        $sessionPostCutoff->update([
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_pelaksanaan' => '2026-10-15',
            'tanggal_terjadwal' => '2026-10-15',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'payment_status' => 'unpaid',
        ]);

        LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $sessionPostCutoff->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Kelas Coding A',
            'pertemuan_ke' => $sessionPostCutoff->nomor_pertemuan,
            'jadwal_mengajar' => $sessionPostCutoff->tanggal_pelaksanaan,
            'jumlah_siswa_hadir' => 10,
            'materi_pengajaran' => 'Conditional Logic',
        ]);

        $response = $this->actingAs($this->admin)->post(
            route('admin.payroll.items.attach-session', $this->payrollItem->id),
            ['session_id' => $sessionPostCutoff->id]
        );

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('payroll_item_session', [
            'payroll_item_id' => $this->payrollItem->id,
            'ekstrakurikuler_session_id' => $sessionPostCutoff->id,
        ]);
        $this->assertEquals('unpaid', $sessionPostCutoff->fresh()->payment_status);
    }

    public function test_admin_can_detach_session_from_slip(): void
    {
        $session = $this->rombel->sessions()->where('nomor_pertemuan', 4)->first();
        $session->update([
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_pelaksanaan' => '2026-10-09',
            'tanggal_terjadwal' => '2026-10-09',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
            'payment_status' => 'unpaid',
        ]);

        LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $session->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'rombel' => 'Kelas Coding A',
            'pertemuan_ke' => $session->nomor_pertemuan,
            'jadwal_mengajar' => $session->tanggal_pelaksanaan,
            'jumlah_siswa_hadir' => 10,
            'materi_pengajaran' => 'Functions',
        ]);

        // Attach dulu
        $this->actingAs($this->admin)->post(
            route('admin.payroll.items.attach-session', $this->payrollItem->id),
            ['session_id' => $session->id]
        );
        $this->assertEquals(1, $this->payrollItem->fresh()->total_sessions);

        // Detach
        $response = $this->actingAs($this->admin)->delete(
            route('admin.payroll.items.detach-session', [$this->payrollItem->id, $session->id])
        );

        $response->assertRedirect(route('payroll.slip.show', $this->payrollItem->id));
        $response->assertSessionHas('success');

        // Pivot terhapus
        $this->assertDatabaseMissing('payroll_item_session', [
            'payroll_item_id' => $this->payrollItem->id,
            'ekstrakurikuler_session_id' => $session->id,
        ]);

        // Sesi kembali ke unpaid
        $this->assertEquals('unpaid', $session->fresh()->payment_status);

        // Total sesi pada slip kembali ke 0
        $this->assertEquals(0, $this->payrollItem->fresh()->total_sessions);
    }
}

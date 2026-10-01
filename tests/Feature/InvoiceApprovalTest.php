<?php

namespace Tests\Feature;

use App\Models\EkstrakurikulerRombel;
use App\Models\InvoiceApproval;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceApprovalTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Model Unit Tests
    // ─────────────────────────────────────────────────────────────────────

    /** @test */
    public function billable_efektif_returns_koreksi_when_set(): void
    {
        $invoice = new InvoiceApproval([
            'jumlah_siswa_billable'  => 20,
            'koreksi_siswa_billable' => 18,
        ]);

        $this->assertEquals(18, $invoice->billable_efektif);
    }

    /** @test */
    public function billable_efektif_falls_back_to_sistem_when_no_koreksi(): void
    {
        $invoice = new InvoiceApproval([
            'jumlah_siswa_billable'  => 20,
            'koreksi_siswa_billable' => null,
        ]);

        $this->assertEquals(20, $invoice->billable_efektif);
    }

    /** @test */
    public function has_koreksi_returns_false_when_null(): void
    {
        $invoice = new InvoiceApproval(['koreksi_siswa_billable' => null]);
        $this->assertFalse($invoice->hasKoreksi());
    }

    /** @test */
    public function has_koreksi_returns_true_when_set(): void
    {
        $invoice = new InvoiceApproval(['koreksi_siswa_billable' => 5]);
        $this->assertTrue($invoice->hasKoreksi());
    }

    /** @test */
    public function is_approved_requires_both_approvals(): void
    {
        $invoice = new InvoiceApproval([
            'status'             => 'approved',
            'operasional_status' => 'approved',
            'akunting_status'    => 'approved',
        ]);
        $this->assertTrue($invoice->isApproved());

        $invoicePending = new InvoiceApproval([
            'status'             => 'pending_akunting',
            'operasional_status' => 'approved',
            'akunting_status'    => 'pending',
        ]);
        $this->assertFalse($invoicePending->isApproved());
    }

    /** @test */
    public function status_label_returns_correct_labels(): void
    {
        $cases = [
            'draft'               => 'Draft',
            'pending_operasional' => 'Menunggu Operasional',
            'pending_akunting'    => 'Menunggu Akunting',
            'approved'            => 'Disetujui',
            'rejected'            => 'Ditolak',
        ];

        foreach ($cases as $status => $expectedLabel) {
            $invoice = new InvoiceApproval(['status' => $status]);
            $this->assertEquals($expectedLabel, $invoice->statusLabel(), "Status '{$status}' mismatch");
        }
    }

    /** @test */
    public function catatan_kontrak_contains_no_cancellation_clause(): void
    {
        $text = InvoiceApproval::catatanKontrakText();

        $this->assertStringContainsString('TIDAK DAPAT DIBATALKAN', $text);
        $this->assertStringContainsString('Reschedule resmi', $text);
        $this->assertStringContainsString('CATATAN KOMITMEN KONTRAK', $text);
    }

    /** @test */
    public function sekolah_skema_tagihan_label_returns_correct(): void
    {
        $sekolah = new Sekolah();

        $cases = [
            'bulanan'         => 'Bulanan (Kalender)',
            'semester'        => 'Per Semester (~16 sesi)',
            'tahunan'         => 'Per Tahun (~32 sesi)',
            'per_4_pertemuan' => 'Per 4 Pertemuan (Rolling Batch)',
        ];

        foreach ($cases as $skema => $label) {
            $sekolah->skema_tagihan = $skema;
            $this->assertEquals($label, $sekolah->skemaTagihanLabel(), "Skema '{$skema}' mismatch");
        }
    }

    // ─────────────────────────────────────────────────────────────────────
    // Route Access
    // ─────────────────────────────────────────────────────────────────────

    /** @test */
    public function invoice_index_requires_authentication(): void
    {
        $response = $this->get('/invoice');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_admin_can_access_invoice_index(): void
    {
        $admin    = $this->makeAdmin();
        $response = $this->actingAs($admin)->get('/invoice');
        $response->assertStatus(200);
        $response->assertSee('Manajemen Invoice Tagihan');
    }

    /** @test */
    public function invoice_create_page_accessible_by_admin(): void
    {
        $admin    = $this->makeAdmin();
        $response = $this->actingAs($admin)->get('/invoice/create');
        $response->assertStatus(200);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Koreksi Validation
    // ─────────────────────────────────────────────────────────────────────

    /** @test */
    public function koreksi_requires_alasan_minimum_10_characters(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'                => 'pending_operasional',
            'jumlah_siswa_billable' => 20,
        ]);

        $response = $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/koreksi", [
                'koreksi_siswa_billable' => 18,
                'koreksi_catatan'        => 'Singkat', // < 10 char
            ]);

        $response->assertSessionHasErrors(['koreksi_catatan']);
    }

    /** @test */
    public function koreksi_blocked_on_approved_invoice(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'                => 'approved',
            'operasional_status'    => 'approved',
            'akunting_status'       => 'approved',
            'jumlah_siswa_billable' => 20,
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/koreksi", [
                'koreksi_siswa_billable' => 5,
                'koreksi_catatan'        => 'Mencoba koreksi invoice approved',
            ]);

        $invoice->refresh();
        $this->assertNull($invoice->koreksi_siswa_billable);
    }

    /** @test */
    public function valid_koreksi_saves_audit_trail(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'                => 'pending_operasional',
            'jumlah_siswa_billable' => 20,
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/koreksi", [
                'koreksi_siswa_billable' => 18,
                'koreksi_catatan'        => '2 siswa keluar dari rombel tgl 15 September 2026',
            ]);

        $invoice->refresh();
        $this->assertEquals(18, $invoice->koreksi_siswa_billable);
        $this->assertEquals($admin->id, $invoice->koreksi_by);
        $this->assertNotNull($invoice->koreksi_at);
    }

    /** @test */
    public function reset_koreksi_clears_all_fields(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'                 => 'pending_operasional',
            'jumlah_siswa_billable'  => 20,
            'koreksi_siswa_billable' => 18,
            'koreksi_catatan'        => 'Koreksi test yang cukup panjang',
            'koreksi_by'             => $admin->id,
            'koreksi_at'             => now(),
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/koreksi-reset");

        $invoice->refresh();
        $this->assertNull($invoice->koreksi_siswa_billable);
        $this->assertNull($invoice->koreksi_catatan);
        $this->assertEquals(20, $invoice->billable_efektif); // fallback ke sistem
    }

    // ─────────────────────────────────────────────────────────────────────
    // Approval Flow
    // ─────────────────────────────────────────────────────────────────────

    /** @test */
    public function operasional_approval_advances_to_pending_akunting(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'             => 'pending_operasional',
            'operasional_status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/approve-operasional", [
                'action'  => 'approved',
                'catatan' => 'Data sudah diverifikasi lapangan',
            ]);

        $invoice->refresh();
        $this->assertEquals('pending_akunting', $invoice->status);
        $this->assertEquals('approved', $invoice->operasional_status);
        $this->assertEquals($admin->id, $invoice->operasional_user_id);
        $this->assertNotNull($invoice->operasional_approved_at);
    }

    /** @test */
    public function akunting_approval_sets_fully_approved(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'             => 'pending_akunting',
            'operasional_status' => 'approved',
            'akunting_status'    => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/approve-akunting", [
                'action'  => 'approved',
                'catatan' => '',
            ]);

        $invoice->refresh();
        $this->assertEquals('approved', $invoice->status);
        $this->assertEquals('approved', $invoice->akunting_status);
        $this->assertTrue($invoice->isApproved());
    }

    /** @test */
    public function rejection_sets_status_to_rejected(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'             => 'pending_operasional',
            'operasional_status' => 'pending',
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/approve-operasional", [
                'action'  => 'rejected',
                'catatan' => 'Data tidak valid',
            ]);

        $invoice->refresh();
        $this->assertEquals('rejected', $invoice->status);
        $this->assertEquals('rejected', $invoice->operasional_status);
    }

    /** @test */
    public function pdf_download_blocked_if_not_fully_approved(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status' => 'pending_akunting',
        ]);

        $response = $this->actingAs($admin)
            ->get("/invoice/{$invoice->id}/pdf");

        $response->assertSessionHasErrors(['msg']);
    }

    /** @test */
    public function approve_operasional_blocked_if_wrong_status(): void
    {
        $admin   = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status' => 'pending_akunting', // Bukan pending_operasional
        ]);

        $this->actingAs($admin)
            ->post("/invoice/{$invoice->id}/approve-operasional", [
                'action' => 'approved',
            ]);

        // Status tidak berubah
        $invoice->refresh();
        $this->assertEquals('pending_akunting', $invoice->status);
    }

    // ─────────────────────────────────────────────────────────────────────
    // Skema Tagihan Update
    // ─────────────────────────────────────────────────────────────────────

    /** @test */
    public function admin_can_update_skema_tagihan_sekolah(): void
    {
        $admin   = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create([
            'kodlan'        => 'TST001',
            'skema_tagihan' => 'per_4_pertemuan',
        ]);

        $response = $this->actingAs($admin)
            ->post("/sekolah/TST001/skema-tagihan", [
                'skema_tagihan' => 'bulanan',
            ]);

        $sekolah->refresh();
        $this->assertEquals('bulanan', $sekolah->skema_tagihan);
        $response->assertSessionHas('success');
    }

    /** @test */
    public function skema_tagihan_rejects_invalid_value(): void
    {
        $admin   = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create(['kodlan' => 'TST002']);

        $response = $this->actingAs($admin)
            ->post("/sekolah/TST002/skema-tagihan", [
                'skema_tagihan' => 'tidak_valid_sama_sekali',
            ]);

        $response->assertSessionHasErrors(['skema_tagihan']);
    }

    /** @test */
    public function default_skema_sekolah_baru_is_per_4_pertemuan(): void
    {
        $sekolah = Sekolah::factory()->create();
        $this->assertEquals('per_4_pertemuan', $sekolah->skema_tagihan);
    }

    /** @test */
    public function invoice_index_only_shows_schools_with_ekstrakurikuler_and_formats_kodlan(): void
    {
        $admin = $this->makeAdmin();

        $sekolahWithEkskul = Sekolah::factory()->create([
            'kodlan'      => 'SKL01',
            'namasekolah' => 'Sekolah Aktif Ekskul',
        ]);
        \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolahWithEkskul->kodlan,
        ]);

        $sekolahWithoutEkskul = Sekolah::factory()->create([
            'kodlan'      => 'SKL02',
            'namasekolah' => 'Sekolah Tanpa Program',
        ]);

        $response = $this->actingAs($admin)->get('/invoice');

        $response->assertOk();
        $response->assertSee('[SKL01] Sekolah Aktif Ekskul');
        $response->assertDontSee('[SKL02] Sekolah Tanpa Program');
    }

    /** @test */
    public function ekstrakurikuler_index_searches_by_school_kodlan(): void
    {
        $admin = $this->makeAdmin();

        $sekolah1 = Sekolah::factory()->create(['kodlan' => 'KD999', 'namasekolah' => 'Sekolah Alpha']);
        $ekskul1 = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah1->kodlan]);

        $sekolah2 = Sekolah::factory()->create(['kodlan' => 'KD888', 'namasekolah' => 'Sekolah Beta']);
        $ekskul2 = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah2->kodlan]);

        $response = $this->actingAs($admin)->get('/ekstrakurikuler?search=KD999');

        $response->assertOk();
        $response->assertSee('Sekolah Alpha');
        $response->assertDontSee('Sekolah Beta');
    }

    /** @test */
    public function rombel_with_4_completed_sessions_detected_as_eligible_for_inv_bulan_1(): void
    {
        $sekolah = Sekolah::factory()->create(['skema_tagihan' => 'per_4_pertemuan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah->kodlan]);
        $rombel  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id]);

        $rombel->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update([
            'status' => 'selesai',
        ]);

        $service  = app(\App\Services\InvoiceService::class);
        $eligible = $service->getEligibleInvoiceForRombel($rombel);

        $this->assertNotNull($eligible);
        $this->assertEquals('Inv Bulan 1', $eligible['periode_label']);
        $this->assertEquals(1, $eligible['sesi_dari']);
        $this->assertEquals(4, $eligible['sesi_sampai']);
    }

    /** @test */
    public function rombel_with_bulanan_skema_detected_as_eligible_at_end_of_month(): void
    {
        $sekolah = Sekolah::factory()->create(['skema_tagihan' => 'bulanan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah->kodlan]);
        $rombel  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id]);

        $rombel->sessions()->where('nomor_pertemuan', 1)->update([
            'status'            => 'selesai',
            'tanggal_terjadwal' => '2026-09-08',
        ]);
        $rombel->sessions()->where('nomor_pertemuan', '>', 1)->update([
            'tanggal_terjadwal' => '2026-10-15',
        ]);

        $service  = app(\App\Services\InvoiceService::class);
        // Cek pada tanggal 28 September (akhir bulan)
        $eligible = $service->getEligibleInvoiceForRombel($rombel, \Carbon\Carbon::parse('2026-09-28'));

        $this->assertNotNull($eligible);
        $this->assertEquals('September 2026', $eligible['periode_label']);
    }

    /** @test */
    public function quick_generate_creates_pending_operasional_invoice(): void
    {
        $admin   = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create(['kodlan' => 'QCK01', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah->kodlan]);
        $rombel  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id]);

        $response = $this->actingAs($admin)->post('/invoice/quick-generate', [
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'skema_tagihan'             => 'per_4_pertemuan',
            'periode_label'             => 'Inv Bulan 1',
            'periode_nomor'             => 1,
            'sesi_dari'                 => 1,
            'sesi_sampai'               => 4,
            'tahun_ajaran'              => '2026/2027',
        ]);

        $this->assertDatabaseHas('invoice_approvals', [
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'periode_label'             => 'Inv Bulan 1',
            'status'                    => 'pending_operasional',
            'operasional_status'        => 'pending',
            'akunting_status'           => 'pending',
        ]);

        $invoice = InvoiceApproval::where('ekstrakurikuler_rombel_id', $rombel->id)->first();
        $response->assertRedirect("/invoice/{$invoice->id}");
    }

    /** @test */
    public function bulk_generate_creates_one_invoice_per_sekolah_with_items_per_rombel(): void
    {
        $admin = $this->makeAdmin();

        $sekolah1 = Sekolah::factory()->create(['kodlan' => 'SEK-B1', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul1  = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah1->kodlan,
            'status'         => 'aktif',
        ]);
        $rombel1A = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul1->id, 'nomor_rombel' => 1]);
        $rombel1B = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul1->id, 'nomor_rombel' => 2]);

        $sekolah2 = Sekolah::factory()->create(['kodlan' => 'SEK-B2', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul2  = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah2->kodlan,
            'status'         => 'aktif',
        ]);
        $rombel2A = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul2->id, 'nomor_rombel' => 1]);

        foreach ([$rombel1A, $rombel1B, $rombel2A] as $r) {
            $r->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update([
                'status' => 'selesai',
            ]);
        }

        $response = $this->actingAs($admin)->post('/invoice/bulk-generate');

        // 2 sekolah = 2 invoice
        $this->assertDatabaseCount('invoice_approvals', 2);
        // Total rombel item = 3 (2 untuk sekolah1, 1 untuk sekolah2)
        $this->assertDatabaseCount('invoice_approval_items', 3);

        $invSekolah1 = InvoiceApproval::where('sekolah_kodlan', $sekolah1->kodlan)->first();
        $this->assertNotNull($invSekolah1);
        $this->assertEquals(2, $invSekolah1->total_rombel);
        $this->assertCount(2, $invSekolah1->items);
        $this->assertStringStartsWith('DRAFT-INV/', $invSekolah1->nomor_invoice);

        $response->assertRedirect('/invoice');
    }

    /** @test */
    public function eligible_rombels_are_sorted_by_days_overdue_descending_with_keterlambatan_badge(): void
    {
        $admin = $this->makeAdmin();

        $sekolahA = Sekolah::factory()->create(['kodlan' => 'SEK-A', 'namasekolah' => 'Sekolah A', 'skema_tagihan' => 'per_4_pertemuan']);
        $sekolahB = Sekolah::factory()->create(['kodlan' => 'SEK-B', 'namasekolah' => 'Sekolah B', 'skema_tagihan' => 'per_4_pertemuan']);

        $ekskulA  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolahA->kodlan, 'status' => 'aktif']);
        $ekskulB  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolahB->kodlan, 'status' => 'aktif']);

        $rombelA  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskulA->id, 'nama_rombel' => 'Rombel A']);
        $rombelB  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskulB->id, 'nama_rombel' => 'Rombel B']);

        // Rombel A: selesai tanggal 2026-09-10 (lebih lama / lebih terlambat)
        $rombelA->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update([
            'status' => 'selesai',
            'tanggal_terjadwal' => '2026-09-10',
            'tanggal_pelaksanaan' => '2026-09-10',
        ]);

        // Rombel B: selesai tanggal 2026-09-25 (baru terlambat 5 hari)
        $rombelB->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update([
            'status' => 'selesai',
            'tanggal_terjadwal' => '2026-09-25',
            'tanggal_pelaksanaan' => '2026-09-25',
        ]);

        $service = app(\App\Services\InvoiceService::class);
        $asOf = \Carbon\Carbon::parse('2026-09-30');
        $eligible = $service->getAllEligibleRombels($asOf);

        $this->assertCount(2, $eligible);

        // Yang paling terlambat (Rombel A: 20 hari) harus di urutan pertama
        $this->assertEquals($rombelA->id, $eligible[0]['ekstrakurikuler_rombel_id']);
        $this->assertEquals(20, $eligible[0]['days_overdue']);
        $this->assertEquals('Terlambat 20 hari', $eligible[0]['keterlambatan_label']);
        $this->assertEquals('bg-danger text-white', $eligible[0]['keterlambatan_badge']);

        // Yang lebih sedikit keterlambatan (Rombel B: 5 hari) di urutan kedua
        $this->assertEquals($rombelB->id, $eligible[1]['ekstrakurikuler_rombel_id']);
        $this->assertEquals(5, $eligible[1]['days_overdue']);
        $this->assertEquals('Terlambat 5 hari', $eligible[1]['keterlambatan_label']);
        $this->assertEquals('bg-warning text-dark', $eligible[1]['keterlambatan_badge']);

        // Pastikan tampilan index blade merender badge keterlambatan dan target pembuatan
        $response = $this->actingAs($admin)->get('/invoice');
        $response->assertStatus(200);
        $response->assertSee('Target Invoice');
        $response->assertSee('Keterlambatan');
    }

    /** @test */
    public function only_ekskul_and_pelatihan_are_eligible_and_others_are_excluded(): void
    {
        $admin = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create(['kodlan' => 'SEK-EX', 'skema_tagihan' => 'per_4_pertemuan']);

        // 1. Program Ekskul -> Valid
        $ekskul = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan'   => $sekolah->kodlan,
            'kategori_program' => 'Ekskul Coding Scratch',
            'jenis_program'    => 'ekstrakurikuler',
            'status'           => 'aktif',
        ]);
        $rombelEkskul = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id, 'nomor_rombel' => 1]);
        $rombelEkskul->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        // 2. Program Pelatihan -> Valid
        $pelatihan = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan'   => $sekolah->kodlan,
            'kategori_program' => 'Pelatihan Robotik Microbit',
            'jenis_program'    => 'pelatihan',
            'status'           => 'aktif',
        ]);
        $rombelPelatihan = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $pelatihan->id, 'nomor_rombel' => 1]);
        $rombelPelatihan->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        // 3. Program Free Trial Class -> TIDAK VALID / EXCLUDED
        $trial = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan'   => $sekolah->kodlan,
            'kategori_program' => 'Free Trial Class',
            'jenis_program'    => 'ekstrakurikuler',
            'status'           => 'aktif',
        ]);
        $rombelTrial = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $trial->id, 'nomor_rombel' => 1]);
        $rombelTrial->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        // 4. Program Sosialisasi -> TIDAK VALID / EXCLUDED
        $sosialisasi = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan'   => $sekolah->kodlan,
            'kategori_program' => 'Sosialisasi bersama Sales',
            'jenis_program'    => 'ekstrakurikuler',
            'status'           => 'aktif',
        ]);
        $rombelSos = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $sosialisasi->id, 'nomor_rombel' => 1]);
        $rombelSos->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        $service = app(\App\Services\InvoiceService::class);
        $eligibleList = $service->getAllEligibleRombels();

        // 1 sekolah eligible dengan 2 rombel item: Ekskul dan Pelatihan
        $this->assertCount(1, $eligibleList);
        $this->assertEquals($sekolah->kodlan, $eligibleList[0]['sekolah_kodlan']);
        $this->assertCount(2, $eligibleList[0]['items']);

        $itemRombelIds = collect($eligibleList[0]['items'])->pluck('ekstrakurikuler_rombel_id')->toArray();
        $this->assertContains($rombelEkskul->id, $itemRombelIds);
        $this->assertContains($rombelPelatihan->id, $itemRombelIds);
        $this->assertNotContains($rombelTrial->id, $itemRombelIds);
        $this->assertNotContains($rombelSos->id, $itemRombelIds);

        // Shortcut eligible untuk rombel trial harus null
        $this->assertNull($service->getEligibleInvoiceForRombel($rombelTrial));
        $this->assertNull($service->getEligibleInvoiceForRombel($rombelSos));

        // Quick generate pada program non-ekskul/pelatihan harus ditolak
        $response = $this->actingAs($admin)->post('/invoice/quick-generate', [
            'ekstrakurikuler_rombel_id' => $rombelTrial->id,
            'skema_tagihan'             => 'per_4_pertemuan',
            'periode_label'             => 'Inv Bulan 1',
        ]);
        $response->assertSessionHasErrors('msg');

        // Endpoint rombels-by-sekolah hanya me-return Ekskul dan Pelatihan
        $responseApi = $this->actingAs($admin)->getJson("/invoice/rombels-by-sekolah?sekolah={$sekolah->kodlan}");
        $responseApi->assertStatus(200);
        $apiRombelIds = collect($responseApi->json())->pluck('id')->toArray();

        $this->assertContains($rombelEkskul->id, $apiRombelIds);
        $this->assertContains($rombelPelatihan->id, $apiRombelIds);
        $this->assertNotContains($rombelTrial->id, $apiRombelIds);
        $this->assertNotContains($rombelSos->id, $apiRombelIds);
    }

    /** @test */
    public function invoice_generated_as_draft_and_finalized_on_accounting_approval(): void
    {
        $admin   = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create(['kodlan' => 'SEK-DFT', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'status'         => 'aktif',
        ]);
        $rombel  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id]);
        $rombel->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        // Generate invoice
        $service = app(\App\Services\InvoiceService::class);
        $invoice = $service->createInvoiceForSekolah($sekolah->kodlan);

        $this->assertNotNull($invoice);
        // Nomor invoice awal harus memiliki prefix DRAFT-
        $this->assertStringStartsWith('DRAFT-INV/', $invoice->nomor_invoice);
        $this->assertEquals('pending_operasional', $invoice->status);

        // Operasional menyetujui
        $this->actingAs($admin)->post("/invoice/{$invoice->id}/approve-operasional", [
            'action' => 'approved',
        ]);
        $invoice->refresh();
        $this->assertStringStartsWith('DRAFT-INV/', $invoice->nomor_invoice);
        $this->assertEquals('pending_akunting', $invoice->status);

        // Akunting menyetujui -> DRAFT- dilepas menjadi nomor resmi
        $this->actingAs($admin)->post("/invoice/{$invoice->id}/approve-akunting", [
            'action' => 'approved',
        ]);
        $invoice->refresh();
        $this->assertEquals('approved', $invoice->status);
        $this->assertStringStartsNotWith('DRAFT-', $invoice->nomor_invoice);
        $this->assertStringStartsWith('INV/', $invoice->nomor_invoice);
    }

    /** @test */
    public function item_level_koreksi_updates_individual_rombel_and_invoice_billable(): void
    {
        $admin   = $this->makeAdmin();
        $sekolah = Sekolah::factory()->create(['kodlan' => 'SEK-ITM', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $sekolah->kodlan,
            'status'         => 'aktif',
        ]);
        $rombel1 = EkstrakurikulerRombel::factory()->create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel'       => 1,
            'jumlah_siswa'       => 10,
        ]);
        $rombel2 = EkstrakurikulerRombel::factory()->create([
            'ekstrakurikuler_id' => $ekskul->id,
            'nomor_rombel'       => 2,
            'jumlah_siswa'       => 15,
        ]);

        $rombel1->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);
        $rombel2->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update(['status' => 'selesai']);

        $service = app(\App\Services\InvoiceService::class);
        $invoice = $service->createInvoiceForSekolah($sekolah->kodlan);

        $this->assertEquals(25, $invoice->billable_efektif);
        $this->assertCount(2, $invoice->items);

        $item1 = $invoice->items()->where('ekstrakurikuler_rombel_id', $rombel1->id)->first();
        $this->assertEquals(10, $item1->jumlah_siswa_billable);

        // Koreksi item 1 menjadi 8 siswa
        $this->actingAs($admin)->post("/invoice/{$invoice->id}/koreksi", [
            'invoice_approval_item_id' => $item1->id,
            'koreksi_siswa_billable'   => 8,
            'koreksi_catatan'          => '2 siswa tidak masuk tagihan rombel 1',
        ]);

        $item1->refresh();
        $this->assertEquals(8, $item1->koreksi_siswa_billable);
        $this->assertEquals(8, $item1->billable_efektif);
        $this->assertEquals('2 siswa tidak masuk tagihan rombel 1', $item1->koreksi_catatan);

        // Total billable efektif invoice sekarang 8 + 15 = 23
        $invoice->refresh();
        $this->assertEquals(23, $invoice->billable_efektif);
        $this->assertTrue($invoice->hasKoreksi());
    }

    /** @test */
    public function operasional_approval_saves_pic_confirmation_and_checklists(): void
    {
        $admin = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'             => InvoiceApproval::STATUS_PENDING_OPERASIONAL,
            'operasional_status' => 'pending',
            'akunting_status'    => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/invoice/{$invoice->id}/approve-operasional", [
            'action'                 => 'approved',
            'catatan'                => 'Semua presensi telah dicek',
            'is_konfirmasi_pic'      => '1',
            'pic_konfirmasi_nama'    => 'Ibu Maria (Wakasek Kurikulum)',
            'pic_konfirmasi_catatan' => 'Konfirmasi via WhatsApp jam 10:00',
            'operasional_checklist'  => [
                'presensi_diverifikasi' => true,
                'materi_tersampaikan'   => true,
                'billable_sesuai_pic'   => true,
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $invoice->refresh();

        $this->assertEquals('approved', $invoice->operasional_status);
        $this->assertEquals(InvoiceApproval::STATUS_PENDING_AKUNTING, $invoice->status);
        $this->assertTrue($invoice->is_konfirmasi_pic);
        $this->assertEquals('Ibu Maria (Wakasek Kurikulum)', $invoice->pic_konfirmasi_nama);
        $this->assertEquals('Konfirmasi via WhatsApp jam 10:00', $invoice->pic_konfirmasi_catatan);
        $this->assertIsArray($invoice->operasional_checklist);
        $this->assertTrue($invoice->operasional_checklist['presensi_diverifikasi']);
    }

    /** @test */
    public function akunting_approval_saves_invoice_tercetak_and_checklists(): void
    {
        $admin = $this->makeAdmin();
        $invoice = InvoiceApproval::factory()->create([
            'status'             => InvoiceApproval::STATUS_PENDING_AKUNTING,
            'operasional_status' => 'approved',
            'akunting_status'    => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/invoice/{$invoice->id}/approve-akunting", [
            'action'              => 'approved',
            'catatan'             => 'Invoice tercetak rangkap 2',
            'is_invoice_tercetak' => '1',
            'akunting_checklist'  => [
                'rekening_valid'       => true,
                'nominal_tarif_sesuai' => true,
                'berkas_siap_edar'     => true,
            ],
        ]);

        $response->assertSessionHasNoErrors();
        $invoice->refresh();

        $this->assertEquals('approved', $invoice->akunting_status);
        $this->assertEquals(InvoiceApproval::STATUS_APPROVED, $invoice->status);
        $this->assertTrue($invoice->is_invoice_tercetak);
        $this->assertIsArray($invoice->akunting_checklist);
        $this->assertTrue($invoice->akunting_checklist['rekening_valid']);
    }

    /** @test */
    public function invoice_table_headers_match_user_exact_specifications(): void
    {
        $admin = $this->makeAdmin();

        $sekolah = Sekolah::factory()->create(['kodlan' => 'SEK-TBL', 'namasekolah' => 'Sekolah Tabel Test', 'skema_tagihan' => 'per_4_pertemuan']);
        $ekskul  = \App\Models\Ekstrakurikuler::factory()->create(['sekolah_kodlan' => $sekolah->kodlan, 'status' => 'aktif']);
        $rombel  = EkstrakurikulerRombel::factory()->create(['ekstrakurikuler_id' => $ekskul->id, 'nama_rombel' => 'Rombel Tabel']);
        $rombel->sessions()->whereBetween('nomor_pertemuan', [1, 4])->update([
            'status' => 'selesai',
            'tanggal_terjadwal' => '2026-09-15',
            'tanggal_pelaksanaan' => '2026-09-15',
        ]);

        $response = $this->actingAs($admin)->get('/invoice');

        $response->assertStatus(200);
        $response->assertSeeText('Sekolah');
        $response->assertSeeText('Rombel & Program (Item)', false);
        $response->assertSeeText('Skema Tagihan');
        $response->assertSeeText('Periode Tagihan');
        $response->assertSeeText('Total Siswa Billable');
        $response->assertSeeText('Target Invoice');
        $response->assertSeeText('Keterlambatan');
        $response->assertSeeText('Semua Skema');
    }
}


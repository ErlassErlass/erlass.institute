<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RescheduleDropdownTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Sekolah $sekolah;
    protected Ekstrakurikuler $ekskul;
    protected EkstrakurikulerRombel $rombel;
    protected EkstrakurikulerSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'verification_status' => 'approved',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'namasekolah' => 'SMPK Test Reschedule',
        ]);

        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'kategori_program' => 'Coding Kids',
            'status' => 'aktif',
        ]);

        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel A',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 15,
            'hari' => 'jumat',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2027-06-30',
            'total_pertemuan' => 16,
            'status' => EkstrakurikulerRombel::STATUS_BERLANGSUNG,
        ]);

        $this->rombel->sessions()->forceDelete();

        $this->session = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->admin->id,
            'nomor_pertemuan' => 1,
            'tanggal_terjadwal' => '2026-08-07',
            'jam_mulai_terjadwal' => '13:00',
            'jam_selesai_terjadwal' => '15:00',
            'status' => 'terjadwal',
        ]);
    }

    public function test_dashboard_modal_contains_pure_reschedule_dropdown_with_standard_options()
    {
        $view = $this->actingAs($this->admin)->view('dashboard.partials.modal-reschedule');

        // Ensure dashRescheduleReason select dropdown exists
        $view->assertSee('id="dashRescheduleReason"', false);
        $view->assertSee('Pilih Alasan Reschedule');
        $view->assertSee('Libur Nasional');
        $view->assertSee('Libur Sekolah');
        $view->assertSee('Permintaan PIC');
        $view->assertSee('Ujian');
        $view->assertSee('Acara Sekolah');
        $view->assertSee('Tidak Diketahui');
    }

    public function test_session_detail_contains_pure_reschedule_dropdown_with_standard_options()
    {
        $response = $this->actingAs($this->admin)->get(route('ekstrakurikuler.sessions.show', $this->session));
        $response->assertStatus(200);

        // Ensure reschedule_reason select dropdown exists
        $response->assertSee('id="reschedule_reason"', false);
        $response->assertSee('Pilih Alasan Reschedule');
        $response->assertSee('Libur Nasional');
        $response->assertSee('Libur Sekolah');
        $response->assertSee('Permintaan PIC');
        $response->assertSee('Ujian');
        $response->assertSee('Acara Sekolah');
        $response->assertSee('Tidak Diketahui');
    }

    public function test_ekstrakurikuler_detail_contains_pure_reschedule_dropdown_with_standard_options()
    {
        $response = $this->actingAs($this->admin)->get(route('ekstrakurikuler.show', $this->ekskul));
        $response->assertStatus(200);

        // Ensure rescheduleAlasan select dropdown exists
        $response->assertSee('id="rescheduleAlasan"', false);
        $response->assertSee('Pilih Alasan Reschedule');
        $response->assertSee('Libur Nasional');
        $response->assertSee('Libur Sekolah');
        $response->assertSee('Permintaan PIC');
        $response->assertSee('Ujian');
        $response->assertSee('Acara Sekolah');
        $response->assertSee('Tidak Diketahui');
    }

    public function test_admin_can_reschedule_with_standard_dropdown_option()
    {
        $options = [
            'Libur Nasional',
            'Libur Sekolah',
            'Permintaan PIC',
            'Ujian',
            'Acara Sekolah',
            'Tidak Diketahui',
        ];

        foreach ($options as $idx => $opt) {
            $sess = EkstrakurikulerSession::create([
                'ekstrakurikuler_id' => $this->ekskul->id,
                'ekstrakurikuler_rombel_id' => $this->rombel->id,
                'user_id_instruktur' => $this->admin->id,
                'nomor_pertemuan' => $idx + 2,
                'tanggal_terjadwal' => '2026-08-' . sprintf('%02d', 10 + $idx),
                'jam_mulai_terjadwal' => '13:00',
                'jam_selesai_terjadwal' => '15:00',
                'status' => 'terjadwal',
            ]);

            $newDate = '2026-09-' . sprintf('%02d', 10 + $idx);
            $response = $this->actingAs($this->admin)
                ->postJson(route('ekstrakurikuler.sessions.reschedule', $sess), [
                    'tanggal_pengganti' => $newDate,
                    'alasan' => $opt,
                    'cascade_shift' => false,
                ]);

            $response->assertStatus(200);
            $response->assertJson(['success' => true]);

            $sess->refresh();
            $this->assertEquals($newDate, $sess->tanggal_terjadwal->format('Y-m-d'));
            $this->assertStringContainsString($opt, $sess->catatan);
        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstrakurikulerSessionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $instructor;
    protected $sekolah;
    protected $ekstrakurikuler;
    protected $rombel;
    protected $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin', 'is_verified' => true]);
        $this->instructor = User::factory()->create(['role' => 'instructor', 'is_verified' => true]);
        $this->sekolah = Sekolah::factory()->create(['kota' => 'Jakarta Pusat']);

        $this->salesman = \App\Models\Salesman::factory()->create(['user_id' => $this->admin->id]);
        $this->ekstrakurikuler = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'user_id_sales' => $this->salesman->id,
            'status' => Ekstrakurikuler::STATUS_AKTIF,
        ]);

        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'nama_rombel' => 'Rombel Test',
            'nomor_rombel' => 1,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'jumlah_siswa' => 0,
            'status' => 'berlangsung',
        ]);

        $this->session = EkstrakurikulerSession::where('ekstrakurikuler_rombel_id', $this->rombel->id)
            ->where('nomor_pertemuan', 1)
            ->first();

        $this->session->user_id_instruktur = $this->instructor->id;
        $this->session->status = EkstrakurikulerSession::STATUS_TERJADWAL;
        $this->session->save();
    }

    public function test_admin_can_postpone_scheduled_session()
    {
        $response = $this->actingAs($this->admin)->postJson(route('ekstrakurikuler.sessions.postpone', $this->session), [
            'alasan' => 'Ada acara rapat sekolah',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Session berhasil ditunda',
            ]);

        $this->session->refresh();
        $this->assertEquals(EkstrakurikulerSession::STATUS_DITUNDA, $this->session->status);
        $this->assertEquals('Ada acara rapat sekolah', $this->session->alasan_pembatalan);
    }

    public function test_instructor_cannot_postpone_scheduled_session()
    {
        $response = $this->actingAs($this->instructor)->postJson(route('ekstrakurikuler.sessions.postpone', $this->session), [
            'alasan' => 'Ada acara rapat sekolah',
        ]);

        $response->assertStatus(403);
        
        $this->session->refresh();
        $this->assertEquals(EkstrakurikulerSession::STATUS_TERJADWAL, $this->session->status);
    }

    public function test_cannot_postpone_non_scheduled_session()
    {
        $this->session->status = EkstrakurikulerSession::STATUS_SELESAI;
        $this->session->save();

        $response = $this->actingAs($this->admin)->postJson(route('ekstrakurikuler.sessions.postpone', $this->session), [
            'alasan' => 'Ada acara rapat sekolah',
        ]);

        $response->assertStatus(400)
            ->assertJson([
                'success' => false,
                'message' => 'Session tidak dapat ditunda saat ini',
            ]);
    }

    public function test_postpone_requires_alasan()
    {
        $response = $this->actingAs($this->admin)->postJson(route('ekstrakurikuler.sessions.postpone', $this->session), []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['alasan']);
    }

    public function test_admin_can_reschedule_postponed_session()
    {
        // 1. Tunda dulu
        $this->session->status = EkstrakurikulerSession::STATUS_DITUNDA;
        $this->session->alasan_pembatalan = 'Rapat';
        $this->session->save();

        // 2. Reschedule ke tanggal baru
        $newDate = now()->addDays(5)->toDateString();
        $response = $this->actingAs($this->admin)->postJson(route('ekstrakurikuler.sessions.reschedule', $this->session), [
            'tanggal_pengganti' => $newDate,
            'alasan' => 'Jadwal baru disepakati',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Session berhasil direschedule',
            ]);

        $this->session->refresh();
        $this->assertEquals(EkstrakurikulerSession::STATUS_TERJADWAL, $this->session->status);
        $this->assertEquals($newDate, $this->session->tanggal_terjadwal->toDateString());
        $this->assertNull($this->session->alasan_pembatalan);
        $this->assertStringContainsString('Rescheduled: Jadwal baru disepakati', $this->session->catatan);
    }

    public function test_check_conflict_ajax_detects_real_schedule_conflicts()
    {
        // 1. Session 1 assigned to instructor
        $this->session->update([
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_terjadwal' => now()->toDateString(),
            'jam_mulai_terjadwal' => '08:00',
            'jam_selesai_terjadwal' => '09:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        // 2. Create another session for another rombel
        $rombel2 = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'nama_rombel' => 'Rombel 2',
            'nomor_rombel' => 2,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:30',
            'hari' => 'senin',
            'jumlah_siswa' => 15,
        ]);

        $session2 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'ekstrakurikuler_rombel_id' => $rombel2->id,
            'nomor_pertemuan' => 99,
            'tanggal_terjadwal' => now()->toDateString(),
            'jam_mulai_terjadwal' => '08:00',
            'jam_selesai_terjadwal' => '09:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        // 3. Test checkConflict AJAX endpoint for session2 trying to select $this->instructor
        $response = $this->actingAs($this->admin)->postJson(route('ekstrakurikuler.sessions.check-conflict', $session2), [
            'user_id_instruktur' => $this->instructor->id,
            'tanggal_terjadwal' => now()->toDateString(),
            'jam_mulai_terjadwal' => '08:00',
            'jam_selesai_terjadwal' => '09:30',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'has_conflict' => true,
            ]);
    }

    public function test_index_filters_are_remembered_and_restored()
    {
        // 1. Visit index with status filter
        $response = $this->actingAs($this->admin)->get(route('ekstrakurikuler.sessions.index', ['status' => 'terjadwal']));
        $response->assertStatus(200);

        // 2. Re-visit index without params -> should redirect to preserved filter URL
        $response2 = $this->actingAs($this->admin)->get(route('ekstrakurikuler.sessions.index'));
        $response2->assertRedirect(route('ekstrakurikuler.sessions.index', ['status' => 'terjadwal']));

        // 3. Visit with reset_filter -> should clear memory and stay on clean index
        $response3 = $this->actingAs($this->admin)->get(route('ekstrakurikuler.sessions.index', ['reset_filter' => 1]));
        $response3->assertRedirect(route('ekstrakurikuler.sessions.index'));
    }
}

<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SessionRescheduleAndHolidayLockTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;
    protected Sekolah $sekolah;
    protected Ekstrakurikuler $ekskul;
    protected EkstrakurikulerRombel $rombel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
            'verification_status' => 'approved',
        ]);

        $this->instructor = User::factory()->create([
            'role' => 'instruktur',
            'verification_status' => 'approved',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'namasekolah' => 'SMPK Ignatius',
        ]);

        $this->ekskul = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'kategori_program' => 'Ekskul Robotika',
            'status' => 'aktif',
        ]);

        $this->rombel = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'nama_rombel' => 'Rombel 1',
            'nomor_rombel' => 1,
            'jumlah_siswa' => 20,
            'hari' => 'jumat',
            'jam_mulai' => '13:30',
            'jam_selesai' => '15:30',
            'tanggal_mulai' => '2026-08-01',
            'tanggal_selesai' => '2027-06-30',
            'total_pertemuan' => 16,
            'status' => EkstrakurikulerRombel::STATUS_BERLANGSUNG,
        ]);

        // Hapus auto-generated sessions agar tes dapat membuat skenario fixture sendiri
        $this->rombel->sessions()->forceDelete();
    }

    public function test_postponed_and_holiday_sessions_do_not_block_subsequent_sessions()
    {
        // Pertemuan 1: Selesai dan sudah ada laporan
        $s1 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 1,
            'tanggal_terjadwal' => '2026-08-14',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_SELESAI,
        ]);

        \App\Models\LaporanMengajar::factory()->create([
            'ekstrakurikuler_session_id' => $s1->id,
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'pertemuan_ke' => 1,
        ]);

        // Pertemuan 2: Ditunda (Minggu lalu libur)
        $s2 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 2,
            'tanggal_terjadwal' => '2026-08-21',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_DITUNDA,
            'alasan_pembatalan' => 'Libur sekolah',
        ]);

        // Pertemuan 3: Terjadwal Hari Ini
        $s3 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 3,
            'tanggal_terjadwal' => Carbon::today()->toDateString(),
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        // Verifikasi getBlockingPriorSession tidak mengembalikan s2 (ditunda)
        $blocking = $s3->getBlockingPriorSession($this->instructor);
        $this->assertNull($blocking, 'Sesi ditunda tidak boleh memblokir sesi berikutnya.');

        // Test jika s2 berstatus libur
        $s2->update(['status' => EkstrakurikulerSession::STATUS_LIBUR]);
        $blockingLibur = $s3->getBlockingPriorSession($this->instructor);
        $this->assertNull($blockingLibur, 'Sesi libur tidak boleh memblokir sesi berikutnya.');
    }

    public function test_national_holiday_date_auto_bypasses_fifo_lock()
    {
        // Register holiday in database
        Holiday::create([
            'tanggal' => '2026-08-17',
            'nama' => 'Hari Kemerdekaan RI',
            'jenis' => Holiday::JENIS_LIBUR_NASIONAL,
            'is_tanggal_merah' => true,
            'tahun' => 2026,
        ]);

        // Sesi lampau yang jatuh pada tanggal merah nasional namun statusnya masih terjadwal
        $sHoliday = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 2,
            'tanggal_terjadwal' => '2026-08-17',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        $sToday = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 3,
            'tanggal_terjadwal' => Carbon::today()->toDateString(),
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        $blocking = $sToday->getBlockingPriorSession($this->instructor);
        $this->assertNull($blocking, 'Sesi lampau yang jatuh pada hari libur nasional otomatis dikecualikan dari blocking.');
    }

    public function test_only_admin_can_reschedule_session()
    {
        $session = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 2,
            'tanggal_terjadwal' => '2026-08-21',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_DITUNDA,
        ]);

        // Instruktur mencoba reschedule -> harus 403 Dilarang
        $this->actingAs($this->instructor)
            ->postJson(route('ekstrakurikuler.sessions.reschedule', $session), [
                'tanggal_pengganti' => '2026-09-04',
                'alasan' => 'Instruktur ingin ganti jadwal',
            ])
            ->assertStatus(403);

        // Admin melakukan reschedule -> harus 200 Sukses
        $response = $this->actingAs($this->admin)
            ->postJson(route('ekstrakurikuler.sessions.reschedule', $session), [
                'tanggal_pengganti' => '2026-09-04',
                'alasan' => 'Jadwal resmi pengganti dari Admin',
            ]);

        $response->assertStatus(200);
        $this->assertEquals('2026-09-04', $session->fresh()->tanggal_terjadwal->format('Y-m-d'));
        $this->assertEquals(EkstrakurikulerSession::STATUS_TERJADWAL, $session->fresh()->status);
    }

    public function test_reschedule_with_cascade_shifts_future_sessions()
    {
        $s2 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 2,
            'tanggal_terjadwal' => '2026-08-21',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_DITUNDA,
        ]);

        $s3 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 3,
            'tanggal_terjadwal' => '2026-08-28',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        $s4 = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 4,
            'tanggal_terjadwal' => '2026-09-04',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        // Admin reschedule s2 dari 21/08/2026 ke 28/08/2026 (+7 hari) dengan cascade
        $this->actingAs($this->admin)
            ->postJson(route('ekstrakurikuler.sessions.reschedule', $s2), [
                'tanggal_pengganti' => '2026-08-28',
                'alasan' => 'Geser berantai 1 minggu',
                'cascade_shift' => true,
            ])
            ->assertStatus(200);

        $this->assertEquals('2026-08-28', $s2->fresh()->tanggal_terjadwal->format('Y-m-d'));
        // S3 geser +7 hari -> 2026-09-04
        $this->assertEquals('2026-09-04', $s3->fresh()->tanggal_terjadwal->format('Y-m-d'));
        // S4 geser +7 hari -> 2026-09-11
        $this->assertEquals('2026-09-11', $s4->fresh()->tanggal_terjadwal->format('Y-m-d'));
    }

    public function test_instructor_and_admin_can_mark_session_as_holiday()
    {
        $session = EkstrakurikulerSession::create([
            'ekstrakurikuler_id' => $this->ekskul->id,
            'ekstrakurikuler_rombel_id' => $this->rombel->id,
            'user_id_instruktur' => $this->instructor->id,
            'nomor_pertemuan' => 2,
            'tanggal_terjadwal' => '2026-08-21',
            'jam_mulai_terjadwal' => '13:30',
            'jam_selesai_terjadwal' => '15:30',
            'status' => EkstrakurikulerSession::STATUS_TERJADWAL,
        ]);

        $this->actingAs($this->instructor)
            ->postJson(route('ekstrakurikuler.sessions.mark-holiday', $session), [
                'alasan' => 'Kegiatan Class Meeting Sekolah',
            ])
            ->assertStatus(200);

        $this->assertEquals(EkstrakurikulerSession::STATUS_LIBUR, $session->fresh()->status);
        $this->assertEquals('Kegiatan Class Meeting Sekolah', $session->fresh()->alasan_pembatalan);
    }
}

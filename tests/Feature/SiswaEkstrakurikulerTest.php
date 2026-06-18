<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\Siswa;
use App\Models\SiswaEkstrakurikuler;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaEkstrakurikulerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sekolah;
    protected $ekstrakurikuler;
    protected $rombelA;
    protected $rombelB;
    protected $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create(['role' => 'admin', 'is_verified' => true]);
        $this->sekolah = Sekolah::factory()->create(['kota' => 'Jakarta Pusat']);

        $this->ekstrakurikuler = Ekstrakurikuler::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
            'user_id_sales' => $this->user->id,
            'status' => Ekstrakurikuler::STATUS_AKTIF,
        ]);

        $this->rombelA = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'nama_rombel' => 'Rombel A',
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

        $this->rombelB = EkstrakurikulerRombel::create([
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'nama_rombel' => 'Rombel B',
            'nomor_rombel' => 2,
            'total_pertemuan' => 12,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonths(3)->toDateString(),
            'hari' => 'selasa',
            'jam_mulai' => '10:00',
            'jam_selesai' => '12:00',
            'jumlah_siswa' => 0,
            'status' => 'berlangsung',
        ]);

        $this->siswa = Siswa::factory()->create([
            'sekolah_kodlan' => $this->sekolah->kodlan,
        ]);
    }

    /** @test */
    public function transfer_creates_pindah_record_and_new_aktif_record()
    {
        // 1. Daftarkan siswa ke Rombel A
        $enrollment = SiswaEkstrakurikuler::create([
            'siswa_id' => $this->siswa->id,
            'ekstrakurikuler_id' => $this->ekstrakurikuler->id,
            'ekstrakurikuler_rombel_id' => $this->rombelA->id,
            'status' => SiswaEkstrakurikuler::STATUS_AKTIF,
            'tanggal_daftar' => now()->subDays(10),
        ]);

        // Refresh rombel count
        $this->rombelA->refresh();
        $this->rombelB->refresh();
        
        $this->assertEquals(1, $this->rombelA->jumlah_siswa);
        $this->assertEquals(0, $this->rombelB->jumlah_siswa);

        // 2. Lakukan transfer rombel ke Rombel B
        $result = $enrollment->transfer($this->rombelB->id, 'Pindah karena bentrok jadwal les');

        $this->assertTrue($result);

        // 3. Verifikasi Database
        // Pastikan ada 2 record keanggotaan sekarang
        $records = SiswaEkstrakurikuler::where('siswa_id', $this->siswa->id)
            ->where('ekstrakurikuler_id', $this->ekstrakurikuler->id)
            ->get();

        $this->assertCount(2, $records);

        // Record 1 (lama): status pindah
        $oldRecord = $records->where('ekstrakurikuler_rombel_id', $this->rombelA->id)->first();
        $this->assertNotNull($oldRecord);
        $this->assertEquals(SiswaEkstrakurikuler::STATUS_PINDAH, $oldRecord->status);
        $this->assertNotNull($oldRecord->tanggal_keluar);
        $this->assertStringContainsString('Pindah ke Rombel ID: ' . $this->rombelB->id, $oldRecord->alasan_keluar);

        // Record 2 (baru): status aktif
        $newRecord = $records->where('ekstrakurikuler_rombel_id', $this->rombelB->id)->first();
        $this->assertNotNull($newRecord);
        $this->assertEquals(SiswaEkstrakurikuler::STATUS_AKTIF, $newRecord->status);
        $this->assertEquals(now()->toDateString(), $newRecord->tanggal_daftar->toDateString());
        $this->assertStringContainsString('Pindahan dari Rombel ID: ' . $this->rombelA->id, $newRecord->catatan);

        // 4. Verifikasi Rombel Student Counts
        $this->rombelA->refresh();
        $this->rombelB->refresh();

        $this->assertEquals(0, $this->rombelA->jumlah_siswa);
        $this->assertEquals(1, $this->rombelB->jumlah_siswa);
    }
}

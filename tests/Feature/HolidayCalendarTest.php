<?php

namespace Tests\Feature;

use App\Models\Holiday;
use App\Services\CalendarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HolidayCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected CalendarService $calendarService;

    public function setUp(): void
    {
        parent::setUp();
        $this->calendarService = app(CalendarService::class);

        // Buat data holiday langsung (tidak tergantung seeder)
        Holiday::insert([
            // Libur nasional
            ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia ke-81', 'jenis' => 'libur_nasional', 'is_tanggal_merah' => 1, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila',                      'jenis' => 'libur_nasional', 'is_tanggal_merah' => 1, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            // Libur agama
            ['tanggal' => '2026-03-20', 'nama' => 'Idul Fitri 1447 Hijriah (Hari 1)', 'jenis' => 'libur_agama', 'is_tanggal_merah' => 1, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-03-21', 'nama' => 'Idul Fitri 1447 Hijriah (Hari 2)', 'jenis' => 'libur_agama', 'is_tanggal_merah' => 1, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            // Cuti bersama — TIDAK dihitung sebagai libur blocking
            ['tanggal' => '2026-03-18', 'nama' => 'Cuti Bersama Idul Fitri 1447 H', 'jenis' => 'cuti_bersama', 'is_tanggal_merah' => 0, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2026-03-23', 'nama' => 'Cuti Bersama Idul Fitri 1447 H', 'jenis' => 'cuti_bersama', 'is_tanggal_merah' => 0, 'tahun' => 2026, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            // 2027
            ['tanggal' => '2027-01-01', 'nama' => 'Tahun Baru Masehi 2027',                     'jenis' => 'libur_nasional', 'is_tanggal_merah' => 1, 'tahun' => 2027, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2027-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia ke-82',  'jenis' => 'libur_nasional', 'is_tanggal_merah' => 1, 'tahun' => 2027, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2027-12-25', 'nama' => 'Hari Raya Natal',                             'jenis' => 'libur_agama',    'is_tanggal_merah' => 1, 'tahun' => 2027, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2027-03-26', 'nama' => 'Wafat Yesus Kristus',                         'jenis' => 'libur_agama',    'is_tanggal_merah' => 1, 'tahun' => 2027, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
            ['tanggal' => '2027-06-05', 'nama' => 'Tahun Baru Islam 1449 Hijriah',               'jenis' => 'libur_agama',    'is_tanggal_merah' => 1, 'tahun' => 2027, 'catatan' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function test_libur_nasional_dihitung_sebagai_hari_libur()
    {
        $this->assertTrue($this->calendarService->isNationalHoliday('2026-08-17'));
        $this->assertTrue(Holiday::isHoliday('2026-08-17'));
    }

    public function test_cuti_bersama_tidak_dihitung_sebagai_hari_libur()
    {
        $this->assertFalse(
            $this->calendarService->isNationalHoliday('2026-03-18'),
            'Cuti bersama seharusnya TIDAK dihitung sebagai hari libur blocking'
        );
        $this->assertFalse(
            Holiday::isHoliday('2026-03-18'),
            'Cuti bersama seharusnya TIDAK dihitung sebagai hari libur blocking'
        );
    }

    public function test_idul_fitri_dihitung_sebagai_hari_libur()
    {
        $this->assertTrue($this->calendarService->isNationalHoliday('2026-03-20'));
    }

    public function test_tanggal_biasa_bukan_hari_libur()
    {
        $this->assertFalse($this->calendarService->isNationalHoliday('2026-07-06'));
    }

    public function test_get_holiday_on_date_mengembalikan_data_yang_benar()
    {
        $holiday = $this->calendarService->getHolidayOnDate('2026-08-17');

        $this->assertNotNull($holiday);
        $this->assertStringContainsString('Kemerdekaan', $holiday->nama);
        $this->assertEquals('libur_nasional', $holiday->jenis);
    }

    public function test_get_holiday_on_date_return_null_untuk_cuti_bersama()
    {
        $holiday = $this->calendarService->getHolidayOnDate('2026-03-18');
        $this->assertNull($holiday, 'getHolidayOnDate seharusnya return null untuk cuti_bersama');
    }

    public function test_get_holidays_in_range_includes_cuti_bersama_untuk_tampilan()
    {
        $holidays = $this->calendarService->getHolidaysInRange('2026-03-01', '2026-03-31');

        $this->assertTrue(
            $holidays->where('jenis', 'cuti_bersama')->count() > 0,
            'Tampilan kalender harus menampilkan cuti_bersama'
        );
        $this->assertTrue(
            $holidays->where('jenis', 'libur_agama')->count() > 0,
            'Tampilan kalender harus menampilkan libur_agama (Idul Fitri)'
        );
    }

    public function test_data_holiday_dua_tahun_tersedia()
    {
        $count2026 = Holiday::byYear(2026)->count();
        $count2027 = Holiday::byYear(2027)->count();

        $this->assertGreaterThan(3, $count2026, '2026 harus punya minimal 4 entri');
        $this->assertGreaterThan(3, $count2027, '2027 harus punya minimal 4 entri');
    }

    public function test_is_safe_to_schedule_false_pada_hari_libur_nasional()
    {
        \App\Models\Sekolah::create([
            'kodlan'      => 'TEST01',
            'namasekolah' => 'Sekolah Test',
            'jenjang'     => 'SD',
            'status'      => 'Aktif',
            'kec'         => 'Kec Test',
            'kotkab'      => 'Kota Test',
            'kota'        => 'Kota Test',
            'provinsi'    => 'Jawa Barat',
        ]);

        $isSafe = $this->calendarService->isSafeToSchedule('2026-08-17', 'TEST01');
        $this->assertFalse($isSafe, 'Tidak aman menjadwalkan pada HUT RI');
    }

    public function test_is_safe_to_schedule_true_pada_cuti_bersama()
    {
        \App\Models\Sekolah::create([
            'kodlan'      => 'TEST02',
            'namasekolah' => 'Sekolah Test 2',
            'jenjang'     => 'SD',
            'status'      => 'Aktif',
            'kec'         => 'Kec Test',
            'kotkab'      => 'Kota Test',
            'kota'        => 'Kota Test',
            'provinsi'    => 'Jawa Barat',
        ]);

        $isSafe = $this->calendarService->isSafeToSchedule('2026-03-18', 'TEST02');
        $this->assertTrue($isSafe, 'Cuti bersama = masih aman untuk jadwalkan sesi');
    }
}

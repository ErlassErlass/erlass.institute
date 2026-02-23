<?php

namespace Tests\Unit;

use App\Models\Absensi;
use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceServiceTest extends TestCase
{
    use RefreshDatabase;

    private AttendanceService $attendanceService;

    private User $instructor;

    private Sekolah $sekolah;

    private Siswa $siswa1;

    private Siswa $siswa2;

    private LaporanMengajar $laporanMengajar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attendanceService = new AttendanceService;

        // Create test data
        $this->instructor = User::factory()->create([
            'role' => 'instruktur',
        ]);

        $this->sekolah = Sekolah::factory()->create([
            'kodlan' => 'TEST001',
            'namasekolah' => 'Test School',
        ]);

        $this->siswa1 = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1',
        ]);

        $this->siswa2 = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1',
        ]);

        $this->laporanMengajar = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'A1',
            'jadwal_mengajar' => Carbon::today(),
        ]);
    }

    public function test_can_calculate_dropouts_with_no_consecutive_absences(): void
    {
        // Create attendance records with mixed attendance
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true,
        ]);

        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false,
        ]);

        $dropoutCount = $this->attendanceService->calculateDropouts($this->laporanMengajar);

        $this->assertEquals(0, $dropoutCount, 'Should have no dropouts with non-consecutive absences');
    }

    public function test_can_calculate_dropouts_with_consecutive_absences(): void
    {
        // Reports are created from newest to oldest: sub1 (yesterday), sub2, sub3, sub4
        // The main report (laporanMengajar) in setUp is sub0 (today).
        $reports = [];
        for ($i = 1; $i <= 4; $i++) {
            $reports[] = LaporanMengajar::factory()->create([
                'user_id_instruktur' => $this->instructor->id,
                'sekolah_kodlan' => 'TEST001',
                'rombel' => 'A1',
                'jadwal_mengajar' => Carbon::today()->subDays($i),
            ]);
        }

        // Student 1: Present in all sessions
        foreach ($reports as $report) {
            Absensi::create([
                'laporan_mengajar_id' => $report->id,
                'siswa_id' => $this->siswa1->id,
                'hadir' => true,
            ]);
        }

        // Student 2: Absent in current report (today) AND last 2 consecutive previous sessions (total 3)
        // Main report: Absent
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false,
        ]);

        // Previous reports: sub1 (absent), sub2 (absent)
        foreach (range(0, 1) as $index) {
            Absensi::create([
                'laporan_mengajar_id' => $reports[$index]->id,
                'siswa_id' => $this->siswa2->id,
                'hadir' => false,
            ]);
        }

        // Present in report sub3 to break streak
        Absensi::create([
            'laporan_mengajar_id' => $reports[2]->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => true,
        ]);

        $dropoutCount = $this->attendanceService->calculateDropouts($this->laporanMengajar);

        $this->assertEquals(1, $dropoutCount, 'Should detect 1 dropout with 3+ consecutive absences');
    }

    public function test_can_get_absent_students(): void
    {
        // Create attendance records
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true,
        ]);

        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false,
        ]);

        $absentStudents = $this->attendanceService->getAbsentStudents($this->laporanMengajar);

        $this->assertCount(1, $absentStudents);
        $this->assertEquals($this->siswa2->id, $absentStudents->first()->id);
    }

    public function test_can_get_present_students(): void
    {
        // Create attendance records
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true,
        ]);

        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false,
        ]);

        $presentStudents = $this->attendanceService->getPresentStudents($this->laporanMengajar);

        $this->assertCount(1, $presentStudents);
        $this->assertEquals($this->siswa1->id, $presentStudents->first()->id);
    }

    public function test_calculates_attendance_statistics_correctly(): void
    {
        // Create attendance records
        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa1->id,
            'hadir' => true,
        ]);

        Absensi::create([
            'laporan_mengajar_id' => $this->laporanMengajar->id,
            'siswa_id' => $this->siswa2->id,
            'hadir' => false,
        ]);

        $stats = $this->attendanceService->calculateAttendanceStats($this->laporanMengajar);

        $this->assertEquals(1, $stats['present_count']);
        $this->assertEquals(1, $stats['absent_count']);
        $this->assertEquals(2, $stats['total_students']);
        $this->assertEquals(50.0, $stats['attendance_percentage']);
    }

    public function test_handles_empty_attendance_records(): void
    {
        // No attendance records created

        $dropoutCount = $this->attendanceService->calculateDropouts($this->laporanMengajar);
        $absentStudents = $this->attendanceService->getAbsentStudents($this->laporanMengajar);
        $presentStudents = $this->attendanceService->getPresentStudents($this->laporanMengajar);
        $stats = $this->attendanceService->calculateAttendanceStats($this->laporanMengajar);

        $this->assertEquals(0, $dropoutCount);
        $this->assertCount(0, $absentStudents);
        $this->assertCount(0, $presentStudents);
        $this->assertEquals(0, $stats['present_count']);
        $this->assertEquals(0, $stats['absent_count']);
        $this->assertEquals(0, $stats['total_students']);
        $this->assertEquals(0, $stats['attendance_percentage']);
    }

    public function test_dropout_calculation_with_different_rombel(): void
    {
        // Create student in different rombel
        $siswa3 = Siswa::factory()->create([
            'sekolah_kodlan' => 'TEST001',
            'rombel' => 'B1',  // Different rombel
        ]);

        // Create multiple reports
        $reports = [];
        for ($i = 0; $i < 4; $i++) {
            $reports[] = LaporanMengajar::factory()->create([
                'user_id_instruktur' => $this->instructor->id,
                'sekolah_kodlan' => 'TEST001',
                'rombel' => 'A1',  // Same rombel as main report
                'jadwal_mengajar' => Carbon::today()->subDays($i),
            ]);
        }

        // Make siswa3 absent in all sessions (but different rombel)
        foreach ($reports as $report) {
            Absensi::create([
                'laporan_mengajar_id' => $report->id,
                'siswa_id' => $siswa3->id,
                'hadir' => false,
            ]);
        }

        $dropoutCount = $this->attendanceService->calculateDropouts($this->laporanMengajar);

        $this->assertEquals(0, $dropoutCount, 'Should not count dropouts from different rombel');
    }
}

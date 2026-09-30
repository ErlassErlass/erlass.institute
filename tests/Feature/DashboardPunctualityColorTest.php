<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardPunctualityColorTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_red_when_report_rate_below_100(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_sistem',
            'email' => 'admin.dashboard@erlass.institute',
        ]);

        $defaultData = [
            'total_sekolah' => 10,
            'total_rombel' => 20,
            'total_siswa' => 300,
            'laporan_hari_ini' => 5,
            'total_instruktur' => 15,
            'fonnte_status' => [
                'connected' => true,
                'device' => 'Erlass WA',
                'quota' => 5000,
            ],
        ];

        $view = $this->actingAs($admin)->view('dashboard.partials.admin-stats', array_merge($defaultData, [
            'corporate_punctuality' => [
                'checkin_rate' => 100,
                'checkin_on_time_count' => 100,
                'checkin_late_count' => 0,
                'report_rate' => 91,
                'report_on_time_count' => 1135,
                'report_late_count' => 115,
                'corporate_rate' => 91,
                'on_time_count' => 1135,
                'late_count' => 115,
            ],
        ]));

        // Ketepatan Laporan: 91% must be red (#dc2626)
        $view->assertSee('Ketepatan Laporan (SLA H+1)');
        $view->assertSee('91%');
        $view->assertSee('style="color: #dc2626;"', false);
        $view->assertSee('border-top: 4px solid #dc2626', false);

        // Presensi Check-in: 100% must be green (#10b981)
        $view->assertSee('Presensi Check-in Sesi');
        $view->assertSee('100%');
        $view->assertSee('style="color: #10b981;"', false);
        $view->assertSee('border-top: 4px solid #10b981', false);
    }

    public function test_admin_dashboard_renders_green_when_report_rate_is_100(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_sistem',
            'email' => 'admin.dashboard2@erlass.institute',
        ]);

        $defaultData = [
            'total_sekolah' => 10,
            'total_rombel' => 20,
            'total_siswa' => 300,
            'laporan_hari_ini' => 5,
            'total_instruktur' => 15,
            'fonnte_status' => [
                'connected' => true,
                'device' => 'Erlass WA',
                'quota' => 5000,
            ],
        ];

        $view = $this->actingAs($admin)->view('dashboard.partials.admin-stats', array_merge($defaultData, [
            'corporate_punctuality' => [
                'checkin_rate' => 100,
                'checkin_on_time_count' => 100,
                'checkin_late_count' => 0,
                'report_rate' => 100,
                'report_on_time_count' => 1250,
                'report_late_count' => 0,
                'corporate_rate' => 100,
                'on_time_count' => 1250,
                'late_count' => 0,
            ],
        ]));

        // Ketepatan Laporan: 100% must be green (#10b981)
        $view->assertSee('Ketepatan Laporan (SLA H+1)');
        $view->assertSee('100%');
        $view->assertSee('style="color: #10b981;"', false);
        $view->assertSee('border-top: 4px solid #10b981', false);
        $view->assertDontSee('#dc2626');
    }
}

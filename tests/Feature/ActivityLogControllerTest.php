<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $webmaster;
    private User $instructor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->webmaster = User::factory()->create([
            'role' => 'webmaster',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);

        $this->instructor = User::factory()->create([
            'role' => 'instruktur',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);
    }

    public function test_webmaster_can_access_activity_logs_page(): void
    {
        ActivityLog::create([
            'user_id' => $this->webmaster->id,
            'action' => 'update',
            'description' => 'Test pergerakan webmaster',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($this->webmaster)
            ->get(route('admin.activity-logs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.activity_logs.index');
        $response->assertSee('Log Audit Pergerakan Admin');
        $response->assertSee('Test pergerakan webmaster');
    }

    public function test_non_webmaster_cannot_access_activity_logs(): void
    {
        $response = $this->actingAs($this->instructor)
            ->get(route('admin.activity-logs.index'));

        $response->assertStatus(403);
    }
}

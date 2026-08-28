<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpCenterTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_help_center_page(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instruktur',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($instructor)
            ->get(route('help.index'));

        $response->assertStatus(200);
        $response->assertViewIs('help.index');
        $response->assertSee('Panduan & FAQ 101');
        $response->assertSee('Cara Membuat Laporan');
    }

    public function test_admin_can_access_admin_guide_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.guide.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.guide.index');
        $response->assertSee('Panduan Operasional &amp; SOP Sistem Administrator', false);
        $response->assertSee('To-Do List Antrean Reschedule');
    }

    public function test_instructor_cannot_access_admin_guide_page(): void
    {
        $instructor = User::factory()->create([
            'role' => 'instruktur',
            'status' => 'Aktif',
            'is_verified' => true,
        ]);

        $response = $this->actingAs($instructor)
            ->get(route('admin.guide.index'));

        $response->assertStatus(403);
    }
}

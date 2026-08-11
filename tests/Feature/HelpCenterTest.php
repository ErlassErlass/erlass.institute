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
        $response->assertSee('Pusat Bantuan & Panduan FAQ 101');
        $response->assertSee('Cara Membuat Laporan');
    }
}

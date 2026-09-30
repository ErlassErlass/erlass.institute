<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sistem_ahmadyusril_can_view_user_list_and_user_details(): void
    {
        // Ahmad Yusril (role: admin_sistem)
        $yusril = User::factory()->create([
            'nama_lengkap' => 'Ahmad Yusril Firdaus',
            'email' => 'ahmad.firdaus@erlass.institute',
            'role' => 'admin_sistem',
        ]);

        $targetUser = User::factory()->create([
            'nama_lengkap' => 'Hanri Basel',
            'email' => 'hanribasel@yahoo.com',
            'role' => 'instruktur',
        ]);

        // Yusril can access /users
        $responseIndex = $this->actingAs($yusril)->get(route('users.index'));
        $responseIndex->assertOk();
        $responseIndex->assertSee('Hanri Basel');

        // Yusril can access /users/{user} (view details)
        $responseShow = $this->actingAs($yusril)->get(route('users.show', $targetUser));
        $responseShow->assertOk();
        $responseShow->assertSee('Hanri Basel');
        $responseShow->assertSee('hanribasel@yahoo.com');
    }

    public function test_regular_instructor_cannot_view_user_list_or_other_users(): void
    {
        $instructor1 = User::factory()->create([
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        $instructor2 = User::factory()->create([
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
        ]);

        // Cannot view user index
        $responseIndex = $this->actingAs($instructor1)->get(route('users.index'));
        $responseIndex->assertForbidden();

        // Cannot view other user details
        $responseShow = $this->actingAs($instructor1)->get(route('users.show', $instructor2));
        $responseShow->assertForbidden();
    }
}

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

    public function test_admin_can_view_edit_and_update_user_competency_dropdowns(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin_sistem',
        ]);

        $instructor = User::factory()->create([
            'role'         => 'instruktur',
            'kompetensi_1' => 'General',
            'kompetensi_2' => null,
            'pend_terakhir'=> 'SMA',
        ]);

        // Akses halaman edit
        $responseEdit = $this->actingAs($admin)->get(route('users.edit', $instructor));
        $responseEdit->assertOk();
        $responseEdit->assertSee('name="kompetensi_1"', false);
        $responseEdit->assertSee('name="kompetensi_2"', false);
        $responseEdit->assertSee('value="Coding"', false);
        $responseEdit->assertSee('value="Robotik"', false);
        $responseEdit->assertSee('value="Desain"', false);
        $responseEdit->assertSee('value="IoT"', false);
        $responseEdit->assertSee('value="Data Science"', false);
        $responseEdit->assertSee('value="Bahasa Inggris"', false);

        // Update kompetensi
        $responseUpdate = $this->actingAs($admin)->put(route('users.update', $instructor), [
            'nama_lengkap'  => $instructor->nama_lengkap,
            'email'         => $instructor->email,
            'tanggal_lahir' => $instructor->tanggal_lahir ? $instructor->tanggal_lahir->format('Y-m-d') : '1995-05-15',
            'status'        => 'Aktif',
            'role'          => 'instruktur',
            'pend_terakhir' => 'SMA/SMK Sederajat',
            'kompetensi_1'  => 'Coding',
            'kompetensi_2'  => 'Bahasa Inggris',
        ]);

        $responseUpdate->assertRedirect();
        $instructor->refresh();
        $this->assertEquals('Coding', $instructor->kompetensi_1);
        $this->assertEquals('Bahasa Inggris', $instructor->kompetensi_2);
    }
}

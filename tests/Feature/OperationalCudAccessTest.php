<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationalCudAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_whitelisted_staff_has_operational_cud_rights()
    {
        $whitelistedStaff = User::factory()->create([
            'email' => 'adinda.wardania@erlass.institute',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $this->assertTrue($whitelistedStaff->canModifyOperationalData());
        $this->assertFalse($whitelistedStaff->isReadOnlyAdmin());
        $this->assertTrue($whitelistedStaff->canApproveGate1Invoice());
        $this->assertFalse($whitelistedStaff->canApproveGate2Invoice());

        $response = $this->actingAs($whitelistedStaff)->get(route('sekolah.create'));
        $response->assertStatus(200);
    }

    public function test_non_whitelisted_admin_is_read_only_and_forbidden_from_cud()
    {
        $readOnlyAdmin = User::factory()->create([
            'email' => 'deborah@erlass.institute',
            'role' => 'admin',
            'is_verified' => true,
        ]);

        $this->assertFalse($readOnlyAdmin->canModifyOperationalData());
        $this->assertTrue($readOnlyAdmin->isReadOnlyAdmin());
        $this->assertFalse($readOnlyAdmin->canApproveGate1Invoice());

        // Can view index (read-only)
        $viewResponse = $this->actingAs($readOnlyAdmin)->get(route('sekolah.index'));
        $viewResponse->assertStatus(200);

        // Cannot access create (CUD blocked with 403)
        $createResponse = $this->actingAs($readOnlyAdmin)->get(route('sekolah.create'));
        $createResponse->assertStatus(403);
    }

    public function test_akunting_has_gate2_but_read_only_on_operational_cud()
    {
        $akuntingStaff = User::factory()->create([
            'email' => 'baguswitono1971@gmail.com',
            'role' => 'akunting',
            'is_verified' => true,
        ]);

        $this->assertFalse($akuntingStaff->canModifyOperationalData());
        $this->assertTrue($akuntingStaff->canApproveGate2Invoice());
        $this->assertFalse($akuntingStaff->canApproveGate1Invoice());

        // Cannot create sekolah
        $createResponse = $this->actingAs($akuntingStaff)->get(route('sekolah.create'));
        $createResponse->assertStatus(403);
    }
}

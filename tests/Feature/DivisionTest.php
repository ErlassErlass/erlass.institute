<?php

namespace Tests\Feature;

use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DivisionTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_division_and_assign_user()
    {
        // Create Division
        $division = Division::create([
            'name' => 'Test Division ' . rand(1, 1000),
            'description' => 'Test Description',
        ]);

        $this->assertDatabaseHas('divisions', ['name' => $division->name]);

        // Create User
        $user = User::factory()->create([
            'division_id' => $division->id,
        ]);

        $this->assertEquals($division->id, $user->division_id);
        $this->assertTrue($user->division->is($division));
        $this->assertTrue($division->users->contains($user));
    }

    public function test_employee_page_is_accessible()
    {
        $admin = User::factory()->create(['role' => 'webmaster']);
        
        $response = $this->actingAs($admin)->get(route('admin.employees.index'));
        
        $response->assertStatus(200);
        $response->assertViewIs('admin.employees.index');
    }
}

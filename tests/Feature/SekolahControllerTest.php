<?php

namespace Tests\Feature;

use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SekolahControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'role' => 'admin_sistem'
        ]);
    }

    public function test_sekolah_index_lists_all_schools(): void
    {
        $sekolah1 = Sekolah::factory()->create([
            'kodlan' => '11111111',
            'namasekolah' => 'SD Negeri 1 Test',
        ]);

        $sekolah2 = Sekolah::factory()->create([
            'kodlan' => '22222222',
            'namasekolah' => 'SMP Swasta 2 Test',
        ]);

        $response = $this->actingAs($this->admin)->get(route('sekolah.index'));

        $response->assertStatus(200);
        $response->assertSee($sekolah1->namasekolah);
        $response->assertSee($sekolah2->namasekolah);
        $response->assertSee($sekolah1->kodlan);
        $response->assertSee($sekolah2->kodlan);
    }

    public function test_sekolah_index_can_search_by_name(): void
    {
        $sekolah1 = Sekolah::factory()->create([
            'kodlan' => '10000001',
            'namasekolah' => 'SD Harapan Bangsa',
        ]);

        $sekolah2 = Sekolah::factory()->create([
            'kodlan' => '10000002',
            'namasekolah' => 'SMP Cipta Karya',
        ]);

        // Search for 'Harapan'
        $response = $this->actingAs($this->admin)->get(route('sekolah.index', ['search' => 'Harapan']));

        $response->assertStatus(200);
        $response->assertSee($sekolah1->namasekolah);
        $response->assertDontSee($sekolah2->namasekolah);
    }

    public function test_sekolah_index_can_search_by_kodlan_npsn(): void
    {
        $sekolah1 = Sekolah::factory()->create([
            'kodlan' => '20604647',
            'namasekolah' => 'SD Negeri Merdeka 1',
        ]);

        $sekolah2 = Sekolah::factory()->create([
            'kodlan' => '20609999',
            'namasekolah' => 'SD Negeri Merdeka 2',
        ]);

        // Search for specific kodlan '20604647'
        $response = $this->actingAs($this->admin)->get(route('sekolah.index', ['search' => '20604647']));

        $response->assertStatus(200);
        $response->assertSee($sekolah1->namasekolah);
        $response->assertDontSee($sekolah2->namasekolah);
        $response->assertSee('20604647');
    }

    public function test_sekolah_index_empty_state_when_not_found(): void
    {
        Sekolah::factory()->create([
            'kodlan' => '12345678',
            'namasekolah' => 'SD Bintang Kejora',
        ]);

        // Search for a keyword that does not match name or kodlan
        $response = $this->actingAs($this->admin)->get(route('sekolah.index', ['search' => '99999999']));

        $response->assertStatus(200);
        $response->assertSee('Tidak ada sekolah dengan nama atau kode tersebut.');
        $response->assertDontSee('SD Bintang Kejora');
    }
}

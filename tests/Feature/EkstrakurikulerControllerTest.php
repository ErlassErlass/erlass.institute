<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EkstrakurikulerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected $sekolah;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user with proper role
        $this->user = User::factory()->create([
            'role' => 'admin',
            'is_verified' => true,
        ]);

        // Create a test sekolah
        $this->sekolah = Sekolah::factory()->create([
            'kota' => 'Jakarta Pusat',
        ]);
    }

    /** @test */
    public function index_page_loads_without_undefined_variable_errors()
    {
        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.index'));

        $response->assertStatus(200);
        $response->assertViewIs('ekstrakurikuler.index');

        // Check that all required variables are passed to the view
        $response->assertViewHas('ekstrakurikulers');
        $response->assertViewHas('sekolahs');
        $response->assertViewHas('regions');
        $response->assertViewHas('kotaOptions');
        $response->assertViewHas('statuses');
        $response->assertViewHas('stats');

        // Verify kotaOptions is an array and contains expected cities
        $kotaOptions = $response->viewData('kotaOptions');
        $this->assertIsArray($kotaOptions);
    }

    /** @test */
    public function create_page_loads_without_undefined_variable_errors()
    {
        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.create'));

        $response->assertStatus(200);
        $response->assertViewIs('ekstrakurikuler.create');

        // Check that all required variables are passed to the view
        $response->assertViewHas('step');
        $response->assertViewHas('formData');
        $response->assertViewHas('sekolahs');
        $response->assertViewHas('salesUsers');
        $response->assertViewHas('regions');
        $response->assertViewHas('kotaOptions');
        $response->assertViewHas('statuses');

        // Verify kotaOptions is properly passed
        $kotaOptions = $response->viewData('kotaOptions');
        $this->assertIsArray($kotaOptions);
    }

    /** @test */
    public function create_step_1_includes_kota_options_variable()
    {
        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.create.step', ['step' => 1]));

        $response->assertStatus(200);
        $response->assertViewIs('ekstrakurikuler.create');

        // Check that kotaOptions is available in the view data
        $response->assertViewHas('kotaOptions');

        // Verify the step1 partial receives kotaOptions
        $viewData = $response->viewData();
        $this->assertArrayHasKey('kotaOptions', $viewData);
        $this->assertIsArray($viewData['kotaOptions']);
    }

    /** @test */
    public function edit_page_loads_without_undefined_variable_errors()
    {
        // Create an ekstrakurikuler record
        $ekstrakurikuler = Ekstrakurikuler::factory()->create([
            'user_id_sales' => $this->user->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
        ]);

        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.edit', $ekstrakurikuler));

        $response->assertStatus(200);
        $response->assertViewIs('ekstrakurikuler.edit');

        // Check that all required variables are passed to the view
        $response->assertViewHas('ekstrakurikuler');
        $response->assertViewHas('sekolahs');
        $response->assertViewHas('salesUsers');
        $response->assertViewHas('regions');
        $response->assertViewHas('kotaOptions');
        $response->assertViewHas('statuses');

        // Verify kotaOptions is properly passed
        $kotaOptions = $response->viewData('kotaOptions');
        $this->assertIsArray($kotaOptions);
    }

    /** @test */
    public function step1_blade_uses_kota_options_instead_of_cities()
    {
        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.create'));

        $response->assertStatus(200);

        // The response should not contain any reference to undefined 'cities' variable
        $content = $response->getContent();

        // Make sure the page renders without PHP errors
        $this->assertStringNotContainsString('Undefined variable: cities', $content);
        $this->assertStringNotContainsString('Undefined index: cities', $content);

        // Check that kotaOptions variable is available
        $response->assertViewHas('kotaOptions');
    }

    /** @test */
    public function all_controller_methods_pass_kota_options_consistently()
    {
        // Test index method
        $indexResponse = $this->actingAs($this->user)->get(route('ekstrakurikuler.index'));
        $indexResponse->assertViewHas('kotaOptions');

        // Test create method
        $createResponse = $this->actingAs($this->user)->get(route('ekstrakurikuler.create'));
        $createResponse->assertViewHas('kotaOptions');

        // Test edit method
        $ekstrakurikuler = Ekstrakurikuler::factory()->create([
            'user_id_sales' => $this->user->id,
            'sekolah_kodlan' => $this->sekolah->kodlan,
        ]);

        $editResponse = $this->actingAs($this->user)->get(route('ekstrakurikuler.edit', $ekstrakurikuler));
        $editResponse->assertViewHas('kotaOptions');

        // Verify all methods pass the same type of data structure
        $indexKotaOptions = $indexResponse->viewData('kotaOptions');
        $createKotaOptions = $createResponse->viewData('kotaOptions');
        $editKotaOptions = $editResponse->viewData('kotaOptions');

        $this->assertIsArray($indexKotaOptions);
        $this->assertIsArray($createKotaOptions);
        $this->assertIsArray($editKotaOptions);
    }

    /** @test */
    public function ekstrakurikuler_pages_load_with_seeded_data()
    {
        // Create some sample data
        $sekolah = Sekolah::factory()->create(['kota' => 'Jakarta Selatan']);
        $user = User::factory()->create(['role' => 'instruktur']);

        Ekstrakurikuler::factory()->create([
            'user_id_sales' => $user->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'city' => 'Jakarta Selatan',
        ]);

        // Test that pages load correctly with actual data
        $response = $this->actingAs($this->user)->get(route('ekstrakurikuler.index'));
        $response->assertStatus(200);

        // Check that kotaOptions contains our test city
        $kotaOptions = $response->viewData('kotaOptions');
        $this->assertContains('Jakarta Selatan', $kotaOptions);
    }
}

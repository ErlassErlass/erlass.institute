<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Sekolah;
use App\Models\Salesman;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $salesmanUser;
    private $salesman;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->salesmanUser = User::factory()->create(['role' => 'sales']);
        $this->salesman = Salesman::create([
            'kode_salesman' => 'SLS001',
            'nama_salesman' => 'Sales Test',
            'user_id' => $this->salesmanUser->id,
            'status_aktif' => true,
        ]);
    }

    public function test_can_create_product_with_date_and_status()
    {
        $product = Product::create([
            'kode_produk' => 'PRD001',
            'nama_produk' => 'Product 1',
            'jenis' => 'Eskul',
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'tanggal' => '2026-06-25',
            'is_aktif' => true,
        ]);

        $this->assertDatabaseHas('products', [
            'kode_produk' => 'PRD001',
            'is_aktif' => true,
            'tanggal' => '2026-06-25 00:00:00',
        ]);
    }

    public function test_product_index_filters_by_status()
    {
        Product::create([
            'kode_produk' => 'PRD-A',
            'nama_produk' => 'Active Product',
            'jenis' => 'Eskul',
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        Product::create([
            'kode_produk' => 'PRD-I',
            'nama_produk' => 'Inactive Product',
            'jenis' => 'Eskul',
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => false,
        ]);

        // Filter active
        $response = $this->actingAs($this->admin)->get(route('products.index', ['filter_status' => 'aktif']));
        $response->assertStatus(200);
        $response->assertSee('Active Product');
        $response->assertDontSee('Inactive Product');

        // Filter inactive
        $response = $this->actingAs($this->admin)->get(route('products.index', ['filter_status' => 'nonaktif']));
        $response->assertStatus(200);
        $response->assertSee('Inactive Product');
        $response->assertDontSee('Active Product');
    }

    public function test_admin_can_toggle_product_status()
    {
        $product = Product::create([
            'kode_produk' => 'PRD-T',
            'nama_produk' => 'Toggle Product',
            'jenis' => 'Eskul',
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('products.toggle-aktif', $product->id));
        
        $response->assertRedirect();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_aktif' => false,
        ]);

        // Toggle back to active
        $this->actingAs($this->admin)->patch(route('products.toggle-aktif', $product->id));
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'is_aktif' => true,
        ]);
    }

    public function test_non_admin_cannot_toggle_product_status()
    {
        $product = Product::create([
            'kode_produk' => 'PRD-T',
            'nama_produk' => 'Toggle Product',
            'jenis' => 'Eskul',
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->salesmanUser)->patch(route('products.toggle-aktif', $product->id));
        $response->assertStatus(403);
    }
}

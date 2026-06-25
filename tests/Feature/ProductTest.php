<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\OrderSp;
use App\Models\OrderItem;
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
            'harga' => 100000,
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
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        Product::create([
            'kode_produk' => 'PRD-I',
            'nama_produk' => 'Inactive Product',
            'jenis' => 'Eskul',
            'harga' => 120000,
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
            'harga' => 100000,
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
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($this->salesmanUser)->patch(route('products.toggle-aktif', $product->id));
        $response->assertStatus(403);
    }

    public function test_cannot_create_order_sp_with_inactive_product()
    {
        $sekolah = Sekolah::create([
            'kodlan' => 'SCH001',
            'namasekolah' => 'School Test',
            'jenjang' => 'SD',
            'status' => 'Aktif',
            'kec' => 'Kec Test',
            'kotkab' => 'Kota Test',
            'kota' => 'Kota Test',
            'provinsi' => 'Jawa Barat',
        ]);

        $activeProduct = Product::create([
            'kode_produk' => 'ACT-01',
            'nama_produk' => 'Active Product',
            'jenis' => 'Eskul',
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        $inactiveProduct = Product::create([
            'kode_produk' => 'INA-01',
            'nama_produk' => 'Inactive Product',
            'jenis' => 'Eskul',
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => false,
        ]);

        $postData = [
            'nomor_sp' => 'SP-2026-001',
            'tanggal_sp' => '2026-06-25',
            'sekolah_kodlan' => $sekolah->kodlan,
            'salesman_id' => $this->salesman->id,
            'jumlah_peserta_estimasi' => 20,
            'jenis_kegiatan' => 'eskul',
            'lokasi_pembelajaran' => 'Sekolah',
            'tanggal_mulai_rencana' => '2026-07-01',
            'jumlah_pertemuan' => 12,
            'catatan_khusus' => '',
            'products' => [
                [
                    'product_id' => $inactiveProduct->id,
                    'harga_satuan' => 100000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->post(route('orders-sp.store'), $postData);
        
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('orders_sp', ['nomor_sp' => 'SP-2026-001']);
    }

    public function test_can_update_order_sp_retaining_existing_inactive_product_but_rejects_new_inactive_product()
    {
        $sekolah = Sekolah::create([
            'kodlan' => 'SCH001',
            'namasekolah' => 'School Test',
            'jenjang' => 'SD',
            'status' => 'Aktif',
            'kec' => 'Kec Test',
            'kotkab' => 'Kota Test',
            'kota' => 'Kota Test',
            'provinsi' => 'Jawa Barat',
        ]);

        $product1 = Product::create([
            'kode_produk' => 'PRD-1',
            'nama_produk' => 'Product 1',
            'jenis' => 'Eskul',
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => true,
        ]);

        $orderSp = OrderSp::create([
            'nomor_sp' => 'SP-2026-002',
            'tanggal_sp' => '2026-06-25',
            'sekolah_kodlan' => $sekolah->kodlan,
            'salesman_id' => $this->salesman->id,
            'jumlah_peserta_estimasi' => 20,
            'jenis_kegiatan' => 'eskul',
            'lokasi_pembelajaran' => 'Sekolah',
            'tanggal_mulai_rencana' => '2026-07-01',
            'jumlah_pertemuan' => 12,
            'status' => 'draft',
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
        ]);

        $orderItem = OrderItem::create([
            'order_sp_id' => $orderSp->id,
            'product_id' => $product1->id,
            'harga_satuan' => 100000,
        ]);

        // Now, make product1 inactive
        $product1->update(['is_aktif' => false]);

        // Create another inactive product
        $product2 = Product::create([
            'kode_produk' => 'PRD-2',
            'nama_produk' => 'Product 2',
            'jenis' => 'Eskul',
            'harga' => 100000,
            'durasi_bulan' => 3,
            'jenis_kegiatan' => 'eskul',
            'standar_durasi_menit' => 60,
            'is_aktif' => false,
        ]);

        // Attempting to update with the existing inactive product should work
        $updateData = [
            'nomor_sp' => 'SP-2026-002',
            'tanggal_sp' => '2026-06-25',
            'sekolah_kodlan' => $sekolah->kodlan,
            'salesman_id' => $this->salesman->id,
            'jumlah_peserta_estimasi' => 25, // changed
            'jenis_kegiatan' => 'eskul',
            'lokasi_pembelajaran' => 'Sekolah',
            'tanggal_mulai_rencana' => '2026-07-01',
            'jumlah_pertemuan' => 12,
            'products' => [
                [
                    'product_id' => $product1->id,
                    'harga_satuan' => 100000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->put(route('orders-sp.update', $orderSp->id), $updateData);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('orders-sp.show', $orderSp->id));
        $this->assertDatabaseHas('orders_sp', [
            'id' => $orderSp->id,
            'jumlah_peserta_estimasi' => 25,
        ]);

        // Attempting to add a NEW inactive product should fail
        $invalidUpdateData = [
            'nomor_sp' => 'SP-2026-002',
            'tanggal_sp' => '2026-06-25',
            'sekolah_kodlan' => $sekolah->kodlan,
            'salesman_id' => $this->salesman->id,
            'jumlah_peserta_estimasi' => 25,
            'jenis_kegiatan' => 'eskul',
            'lokasi_pembelajaran' => 'Sekolah',
            'tanggal_mulai_rencana' => '2026-07-01',
            'jumlah_pertemuan' => 12,
            'products' => [
                [
                    'product_id' => $product1->id,
                    'harga_satuan' => 100000,
                ],
                [
                    'product_id' => $product2->id,
                    'harga_satuan' => 100000,
                ]
            ]
        ];

        $response = $this->actingAs($this->admin)->put(route('orders-sp.update', $orderSp->id), $invalidUpdateData);
        $response->assertSessionHas('error');
    }
}

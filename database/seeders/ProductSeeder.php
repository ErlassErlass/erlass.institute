<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (Product::count() === 0) {
            Product::create([
                'id' => 1,
                'kode_produk' => 'P-SCRATCH',
                'nama_produk' => 'Coding Scratch',
                'jenis' => 'Coding',
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
                'tanggal' => '2026-06-01',
                'is_aktif' => true,
            ]);
            Product::create([
                'id' => 2,
                'kode_produk' => 'P-MICROBIT',
                'nama_produk' => 'Micro:bit Learning Kit',
                'jenis' => 'Coding',
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
                'tanggal' => '2026-06-01',
                'is_aktif' => true,
            ]);
            Product::create([
                'id' => 3,
                'kode_produk' => 'P-ROBOTIK',
                'nama_produk' => 'Robotik Explorer',
                'jenis' => 'Robotik',
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
                'tanggal' => '2026-06-01',
                'is_aktif' => true,
            ]);
            Product::create([
                'id' => 4,
                'kode_produk' => 'P-PYTHON',
                'nama_produk' => 'Python Programming',
                'jenis' => 'Coding',
                'durasi_bulan' => 12,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 90,
                'tanggal' => '2026-06-01',
                'is_aktif' => true,
            ]);
        }
    }
}

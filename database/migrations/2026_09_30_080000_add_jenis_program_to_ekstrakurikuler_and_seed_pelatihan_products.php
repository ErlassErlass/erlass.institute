<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom jenis_program ke tabel ekstrakurikuler
        if (!Schema::hasColumn('ekstrakurikuler', 'jenis_program')) {
            Schema::table('ekstrakurikuler', function (Blueprint $table) {
                $table->enum('jenis_program', ['ekstrakurikuler', 'pelatihan'])
                    ->default('ekstrakurikuler')
                    ->after('kategori_program');
            });
        }

        // 2. Update data eksisting jika ada yang mengandung kata 'Pelatihan'
        DB::table('ekstrakurikuler')
            ->where('kategori_program', 'LIKE', 'Pelatihan%')
            ->update(['jenis_program' => 'pelatihan']);

        // 3. Seed / insert produk pelatihan ke tabel products jika belum ada
        $pelatihanProducts = [
            [
                'kode_produk' => 'PCR',
                'nama_produk' => 'Pelatihan Coding Scratch',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PER',
                'nama_produk' => 'Pelatihan English Course',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PPR',
                'nama_produk' => 'Pelatihan Pictoblox AI',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-MB',
                'nama_produk' => 'Pelatihan Robotik Microbit Beginner',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-ALK',
                'nama_produk' => 'Pelatihan Robotik Arduino Learning Kit',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-MLK',
                'nama_produk' => 'Pelatihan Robotik Microbit Learning Kit',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-RE',
                'nama_produk' => 'Pelatihan Robotik Explorer',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-RB',
                'nama_produk' => 'Pelatihan Robotik Erboblox',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_produk' => 'PRR-RJ',
                'nama_produk' => 'Pelatihan Robotik Jimu',
                'jenis' => 'Pelatihan',
                'durasi_bulan' => 1,
                'jenis_kegiatan' => 'eskul',
                'standar_durasi_menit' => 120,
                'is_aktif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($pelatihanProducts as $prod) {
            $exists = DB::table('products')->where('nama_produk', $prod['nama_produk'])->exists();
            if (!$exists) {
                DB::table('products')->insert($prod);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('ekstrakurikuler', 'jenis_program')) {
            Schema::table('ekstrakurikuler', function (Blueprint $table) {
                $table->dropColumn('jenis_program');
            });
        }

        DB::table('products')->where('jenis', 'Pelatihan')->delete();
    }
};

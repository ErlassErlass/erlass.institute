<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\LaporanMengajar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User
        // Buat 1 Admin Utama
        User::factory()->create([
            'nama_lengkap' => 'Admin Erlass Utama',
            'email' => 'admin@erlass.com',
            'role' => 'admin_erlass'
        ]);
        // Buat 1 Admin biasa
        User::factory()->create([
            'nama_lengkap' => 'Admin Biasa',
            'email' => 'admin.biasa@erlass.com',
            'role' => 'admin'
        ]);
        // Buat 48 Instruktur (total user 50)
        User::factory(48)->create(['role' => 'instruktur']);

        // 2. Buat 80 Sekolah
        Sekolah::factory(80)->create();
        
        // 3. Buat 500 Siswa (secara acak akan masuk ke 80 sekolah di atas)
        Siswa::factory(500)->create();

        // 4. Buat 100 Laporan Mengajar
        // Ini akan secara otomatis membuat data absensi juga berkat configure() di factory-nya.
        LaporanMengajar::factory(100)->create();
    }
}
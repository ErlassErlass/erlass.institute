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
        // 1. Buat User dengan sistem role baru
        
        // Buat 1 Webmaster (akses tertinggi)
        User::factory()->create([
            'nama_lengkap' => 'Webmaster Utama',
            'email' => 'webmaster@erlass.com',
            'role' => 'webmaster'
        ]);
        
        // Buat 1 Admin Erlass (akses terbatas)
        User::factory()->create([
            'nama_lengkap' => 'Admin Erlass',
            'email' => 'admin@erlass.com',
            'role' => 'admin_erlass'
        ]);
        
        // Buat 1 Debug User untuk development
        User::factory()->create([
            'nama_lengkap' => 'Debug User',
            'email' => 'debug@erlass.com',
            'role' => 'debug_user'
        ]);
        
        // Buat 47 Instruktur (total user 50)
        // 40 instruktur terverifikasi
        User::factory(40)->create([
            'role' => 'instruktur',
            'is_verified' => true,
            'verified_at' => now(),
            'verification_status' => 'approved'
        ]);
        
        // 5 instruktur pending verifikasi  
        User::factory(5)->create([
            'role' => 'instruktur',
            'is_verified' => false,
            'verified_at' => null,
            'verification_status' => 'pending'
        ]);
        
        // 2 instruktur ditolak verifikasinya
        User::factory(2)->create([
            'role' => 'instruktur',
            'is_verified' => false,
            'verified_at' => null,
            'verification_status' => 'rejected',
            'rejection_reason' => 'Dokumen tidak lengkap'
        ]);

        // 2. Buat 80 Sekolah
        Sekolah::factory(80)->create();
        
        // 3. Buat 500 Siswa (secara acak akan masuk ke 80 sekolah di atas)
        Siswa::factory(500)->create();

        // 4. Buat 100 Laporan Mengajar
        // Ini akan secara otomatis membuat data absensi juga berkat configure() di factory-nya.
        LaporanMengajar::factory(100)->create();
    }
}
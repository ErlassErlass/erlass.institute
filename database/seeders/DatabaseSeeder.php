<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    $this->call([
        MainSeeder::class,
    ]);

        User::create([
            'nama_lengkap' => 'Admin Sistem',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'tanggal_lahir' => '1990-01-01',
            'no_telephone' => '08123456789',
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Manajemen Sistem',
            'kompetensi_2' => 'Keamanan Jaringan',
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

    }
}

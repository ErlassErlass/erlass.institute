<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder {
    public function run(): void {
        User::factory()->count(10)->create();

        // Create an admin user
        User::create([
            'nama_lengkap' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'tanggal_lahir' => '1990-01-01',
            'no_telephone' => '08123456789',
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S2',
            'kompetensi_1' => 'Management',
            'role' => 'admin',
        ]);
    }
}

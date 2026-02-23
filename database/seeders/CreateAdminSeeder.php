<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Division;

class CreateAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada divisi default, jika belum, ambil yang pertama atau null
        $division = Division::first();

        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama_lengkap' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'webmaster', // Role tertinggi
                'division_id' => $division ? $division->id : null,
                'status' => 'active',
                'no_telephone' => '081234567890',
                'tanggal_lahir' => '1990-01-01',
                'agama' => 'Islam',
                'pend_terakhir' => 'S1',
                'kompetensi_1' => '-',
                'kompetensi_2' => '-',
                'no_telephone' => '081234567890',
            ]
        );
        
        $this->command->info('User Admin created: admin@example.com / password');
    }
}

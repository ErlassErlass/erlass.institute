<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Webmaster user
        // IMPORTANT: Change these passwords in production deployment
        User::create([
            'nama_lengkap' => 'Webmaster Erlass',
            'email' => 'webmaster@erlass.institute',
            'password' => bcrypt(env('WEBMASTER_PASSWORD', 'password')),
            'tanggal_lahir' => '1985-01-01',
            'no_telephone' => '08111111111',
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S2',
            'kompetensi_1' => 'System Administration',
            'kompetensi_2' => 'Database Management',
            'role' => 'webmaster',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'application_date' => now(),
        ]);

        // Create Admin Erlass user
        User::create([
            'nama_lengkap' => 'Admin Erlass',
            'email' => 'admin@erlass.institute',
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
            'tanggal_lahir' => '1988-05-15',
            'no_telephone' => '08222222222',
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Educational Management',
            'kompetensi_2' => 'Data Analysis',
            'role' => 'admin_sistem',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => 1, // Verified by webmaster
            'application_date' => now(),
        ]);

        // Create Instruktur Erlass user (verified)
        User::create([
            'nama_lengkap' => 'Instruktur Erlass',
            'email' => 'instruktur@erlass.institute',
            'password' => bcrypt(env('INSTRUCTOR_PASSWORD', 'password')),
            'tanggal_lahir' => '1992-03-20',
            'no_telephone' => '08333333333',
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Mathematics',
            'kompetensi_2' => 'Computer Science',
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
            'verified_by' => 1, // Verified by webmaster
            'application_date' => now(),
        ]);

        // Create additional sample instruktur (pending verification)
        User::create([
            'nama_lengkap' => 'Instruktur Pending',
            'email' => 'pending@erlass.institute',
            'password' => bcrypt(env('PENDING_PASSWORD', 'password')),
            'tanggal_lahir' => '1990-07-10',
            'no_telephone' => '08444444444',
            'status' => 'active',
            'agama' => 'Kristen',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Physics',
            'kompetensi_2' => 'Chemistry',
            'role' => 'instruktur',
            'is_verified' => false,
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'application_date' => now()->subDays(3),
        ]);

        // Create factory users for testing
        User::factory()->count(5)->create();
    }
}

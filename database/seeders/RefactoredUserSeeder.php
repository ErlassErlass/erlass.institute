<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RefactoredUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Webmaster
        User::updateOrCreate(
            ['email' => 'webmaster@erlass.co.id'],
            [
                'nama_lengkap' => 'Webmaster Utama',
                'password' => Hash::make('password'),
                'role' => 'webmaster',
                'status' => 'active',
                'no_telephone' => '081200000001',
                'tanggal_lahir' => '1990-01-01',
                'email_verified_at' => now(),
            ]
        );

        // 2. Admin Sistem
        User::updateOrCreate(
            ['email' => 'adminsistem@erlass.co.id'],
            [
                'nama_lengkap' => 'Admin Sistem IT',
                'password' => Hash::make('password'),
                'role' => 'admin_sistem',
                'status' => 'active',
                'no_telephone' => '081200000002',
                'tanggal_lahir' => '1992-02-02',
                'email_verified_at' => now(),
            ]
        );

        // 3. Admin Operasional
        User::updateOrCreate(
            ['email' => 'adminops@erlass.co.id'],
            [
                'nama_lengkap' => 'Admin Operasional',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'status' => 'active',
                'no_telephone' => '081200000003',
                'tanggal_lahir' => '1993-03-03',
                'email_verified_at' => now(),
            ]
        );

        // 4. Instruktur (Verified)
        User::updateOrCreate(
            ['email' => 'instruktur@erlass.co.id'],
            [
                'nama_lengkap' => 'Budi Pengajar',
                'password' => Hash::make('password'),
                'role' => 'instruktur',
                'status' => 'active',
                'no_telephone' => '081200000004',
                'tanggal_lahir' => '1994-04-04',
                'email_verified_at' => now(),
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
            ]
        );

         // 5. Instruktur (Unverified) - Optional for testing
         User::updateOrCreate(
            ['email' => 'calon_instruktur@erlass.co.id'],
            [
                'nama_lengkap' => 'Siti Calon',
                'password' => Hash::make('password'),
                'role' => 'instruktur',
                'status' => 'active',
                'no_telephone' => '081200000005',
                'tanggal_lahir' => '1995-05-05',
                'email_verified_at' => now(),
                'is_verified' => false,
                'verification_status' => 'pending',
            ]
        );
    }
}

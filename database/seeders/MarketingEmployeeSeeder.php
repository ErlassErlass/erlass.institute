<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Division;

class MarketingEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Marketing Division
        $marketingDivision = Division::firstOrCreate(
            ['name' => 'Marketing'],
            ['description' => 'Divisi Pemasaran dan Sales']
        );

        $salesPeople = [
            ['nama' => 'Andi Marketing', 'email' => 'andi.sales@erlass.co.id'],
            ['nama' => 'Budi Sales', 'email' => 'budi.sales@erlass.co.id'],
            ['nama' => 'Citra Lestari', 'email' => 'citra.sales@erlass.co.id'],
            ['nama' => 'Dedi Supriadi', 'email' => 'dedi.sales@erlass.co.id'],
            ['nama' => 'Eka Pratiwi', 'email' => 'eka.sales@erlass.co.id'],
        ];

        foreach ($salesPeople as $person) {
            User::firstOrCreate(
                ['email' => $person['email']],
                [
                    'nama_lengkap' => $person['nama'],
                    'password' => Hash::make('password'),
                    'role' => 'admin',
                    'division_id' => $marketingDivision->id,
                    'status' => 'active',
                    'no_telephone' => '08123456789',
                    'email_verified_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('Marketing Employees seeded successfully.');
    }
}

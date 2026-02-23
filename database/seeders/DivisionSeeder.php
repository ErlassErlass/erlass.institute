<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisions = [
            [
                'name' => 'IT & Development',
                'description' => 'Bagian Teknologi Informasi dan Pengembangan Sistem',
            ],
            [
                'name' => 'Akademik & Kurikulum',
                'description' => 'Bagian Pengelolaan Kurikulum dan Kegiatan Belajar Mengajar',
            ],
            [
                'name' => 'HRD & Personalia',
                'description' => 'Bagian Sumber Daya Manusia',
            ],
            [
                'name' => 'Keuangan',
                'description' => 'Bagian Keuangan dan Administrasi',
            ],
            [
                'name' => 'Marketing & Sales',
                'description' => 'Bagian Pemasaran dan Penjualan',
            ],
        ];

        foreach ($divisions as $division) {
            Division::create($division);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class JabodetabekStudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create or Get 5 Schools in Jabodetabek
        $schools = [
            [
                'namasekolah' => 'SMAN 1 Jakarta',
                'kodlan' => 'SMAN1JKT',
                'kotkab' => 'Kota Jakarta Pusat',
                'kec' => 'Gambir',
                'provinsi' => 'DKI Jakarta',
                'jenjang' => 'SMA',
            ],
            [
                'namasekolah' => 'SMA Labschool Kebayoran',
                'kodlan' => 'SMALABS',
                'kotkab' => 'Kota Jakarta Selatan',
                'kec' => 'Kebayoran Baru',
                'provinsi' => 'DKI Jakarta',
                'jenjang' => 'SMA',
            ],
            [
                'namasekolah' => 'SMAN 1 Depok',
                'kodlan' => 'SMAN1DPK',
                'kotkab' => 'Kota Depok',
                'kec' => 'Pancoran Mas',
                'provinsi' => 'Jawa Barat',
                'jenjang' => 'SMA',
            ],
            [
                'namasekolah' => 'SMA Penabur Gading Serpong',
                'kodlan' => 'PENABURGS',
                'kotkab' => 'Kab. Tangerang',
                'kec' => 'Kelapa Dua',
                'provinsi' => 'Banten',
                'jenjang' => 'SMA',
            ],
            [
                'namasekolah' => 'SMAN 1 Kota Bekasi',
                'kodlan' => 'SMAN1BKS',
                'kotkab' => 'Kota Bekasi',
                'kec' => 'Bekasi Barat',
                'provinsi' => 'Jawa Barat',
                'jenjang' => 'SMA',
            ],
        ];

        foreach ($schools as $schoolData) {
            $sekolah = Sekolah::updateOrCreate(
                ['kodlan' => $schoolData['kodlan']],
                $schoolData
            );

            // 2. Create Random Students for each school
            // Let's create 10 students per school for variety
            $classes = ['10-A', '10-B', '11-IPA-1', '11-IPS-1', '12-IPA-1'];

            for ($i = 1; $i <= 10; $i++) {
                $nisn = rand(1000000000, 9999999999);
                $name = $this->generateRandomIndonesianName();
                $rombel = Arr::random($classes);
                
                Siswa::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nama_lengkap' => $name,
                        'sekolah_kodlan' => $sekolah->kodlan,
                        'rombel' => $rombel,
                        'kelamin' => Arr::random(['L', 'P']),
                        'email' => strtolower(str_replace(' ', '', $name)) . rand(1, 99) . '@student.example.com',
                        'notelp_siswa' => '08' . rand(1000000000, 9999999999),
                        'kota_lahir' => $sekolah->kotkab,
                        'tanggal_lahir' => date('Y-m-d', strtotime('-16 years')),
                    ]
                );
            }
        }
    }

    private function generateRandomIndonesianName()
    {
        $firstNames = ['Adi', 'Budi', 'Citra', 'Dewi', 'Eko', 'Fitri', 'Gita', 'Hadi', 'Indah', 'Joko'];
        $lastNames = ['Santoso', 'Wijaya', 'Putri', 'Nugroho', 'Pratama', 'Kusuma', 'Lestari', 'Saputra', 'Hidayat', 'Wibowo'];
        
        return Arr::random($firstNames) . ' ' . Arr::random($lastNames);
    }
}

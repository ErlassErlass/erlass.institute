<?php

namespace Database\Seeders;

use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat User dengan sistem role baru
        $this->call(UserSeeder::class);
        /* 
        // Inline factory blocked replaced by UserSeeder class
        // ...
        */

        /*
        // 2. Buat 80 Sekolah dengan variasi jenjang pendidikan
        Sekolah::factory(50)->create(['jenjang' => 'SD']);
        Sekolah::factory(30)->create(['jenjang' => 'SMP']);

        // 3. Buat 500 Siswa (secara acak akan masuk ke sekolah di atas)
        Siswa::factory(500)->create();

        // 4. Buat Program Ekstrakurikuler
        // 15 program draft dan diajukan
        \App\Models\Ekstrakurikuler::factory(15)->draft()->create();
        \App\Models\Ekstrakurikuler::factory(10)->submitted()->create();

        // 20 program yang sudah disetujui
        \App\Models\Ekstrakurikuler::factory(20)->approved()->create();

        // 25 program aktif berjalan
        \App\Models\Ekstrakurikuler::factory(25)->active()->create();

        // 8 program yang sudah selesai
        \App\Models\Ekstrakurikuler::factory(8)->completed()->create();

        // 5 program yang ditolak atau dibatalkan
        \App\Models\Ekstrakurikuler::factory(3)->rejected()->create();
        \App\Models\Ekstrakurikuler::factory(2)->cancelled()->create();

        // 5. Buat 100 Laporan Mengajar
        // Ini akan secara otomatis membuat data absensi juga berkat configure() di factory-nya.
        LaporanMengajar::factory(100)->create();
        */

        // 2. Import Real Data (Master Data)
        $this->call([
            SekolahSeeder::class,     // Imports DataSekolah.csv
            InstrukturSeeder::class,  // Imports Data Instruktur Erlass 2025.xlsx (70 instructors)
            // ManualSiswaSeeder::class, // Imports siswa_import.csv (Disabled per user request)
            EmployeeSeeder::class,    // Imports employees_import.csv
            RefMateriSeeder::class,   // Syllabus/Materi Dropdowns
        ]);
    }
}

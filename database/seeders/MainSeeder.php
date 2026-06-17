<?php

namespace Database\Seeders;

use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Matikan foreign key check untuk mengosongkan tabel
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        // Sekolah::truncate(); // NOTE: Jangan pernah hapus master data sekolah!
        Siswa::truncate();
        LaporanMengajar::truncate();
        // Anda bisa tambahkan truncate untuk tabel lain jika perlu (absensi, dll)
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // 1. Buat User
        // Buat 2 Admin Erlass
        User::factory()->create([
            'nama_lengkap' => 'Admin Erlass 1',
            'email' => 'adminerlass1@erlass.institute',
            'role' => 'admin_erlass',
        ]);
        User::factory()->create([
            'nama_lengkap' => 'Admin Erlass 2',
            'email' => 'adminerlass2@erlass.institute',
            'role' => 'admin_erlass',
        ]);

        // Buat 8 Admin biasa
        User::factory(8)->create(['role' => 'admin']);

        // Buat 40 Instruktur
        User::factory(40)->create(['role' => 'instruktur']);

        // 2. Buat 80 Sekolah
        Sekolah::factory(80)->create();

        // 3. Buat 500 Siswa (secara acak akan masuk ke 80 sekolah di atas)
        Siswa::factory(500)->create();

        // 4. Buat 100 Laporan Mengajar
        // Ini akan secara otomatis membuat data absensi juga berkat configure() di factory-nya.
        LaporanMengajar::factory(100)->create();
    }
}

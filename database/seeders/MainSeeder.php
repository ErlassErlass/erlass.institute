<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Sekolah;

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
        Sekolah::truncate();
        // Anda bisa tambahkan truncate untuk tabel lain jika perlu (siswa, laporan, dll)
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Membuat Users
        User::create([
            'id' => 1,
            'nama_lengkap' => 'Admin Erlass',
            'email' => 'admin@erlass.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'tanggal_lahir' => '1990-01-01', 'no_telephone' => '081234567890', 'status' => 'Aktif', 'agama' => 'Islam', 'pend_terakhir' => 'S1', 'kompetensi_1' => 'Manajemen'
        ]);
        User::create([
            'id' => 2,
            'nama_lengkap' => 'Budi Instruktur',
            'email' => 'budi.instruktur@erlass.com',
            'password' => Hash::make('password'),
            'role' => 'instruktur',
            'tanggal_lahir' => '1995-05-15', 'no_telephone' => '081209876543', 'status' => 'Aktif', 'agama' => 'Kristen', 'pend_terakhir' => 'S1', 'kompetensi_1' => 'Coding'
        ]);
        User::create([
            'id' => 3,
            'nama_lengkap' => 'Siti Instruktur',
            'email' => 'siti.instruktur@erlass.com',
            'password' => Hash::make('password'),
            'role' => 'instruktur',
            'tanggal_lahir' => '1998-11-20', 'no_telephone' => '081112223334', 'status' => 'Aktif', 'agama' => 'Islam', 'pend_terakhir' => 'S1', 'kompetensi_1' => 'Robotik'
        ]);

        // Membuat Sekolah
        Sekolah::create(['kodlan' => 'JKT-SEL-PSM-001', 'namasekolah' => 'SDN 1 Pasar Minggu', 'jenjang' => 'SD', 'status' => 'Negeri', 'kec' => 'Pasar Minggu', 'kotkab' => 'Kota', 'kota' => 'Jakarta Selatan', 'provinsi' => 'DKI Jakarta']);
        Sekolah::create(['kodlan' => 'JKT-SEL-PSM-002', 'namasekolah' => 'SMP Harapan Bangsa', 'jenjang' => 'SMP', 'status' => 'Swasta', 'kec' => 'Pasar Minggu', 'kotkab' => 'Kota', 'kota' => 'Jakarta Selatan', 'provinsi' => 'DKI Jakarta']);
        Sekolah::create(['kodlan' => 'JKT-TIM-CKG-001', 'namasekolah' => 'SDN Cilangkap 02 Pagi', 'jenjang' => 'SD', 'status' => 'Negeri', 'kec' => 'Cipayung', 'kotkab' => 'Kota', 'kota' => 'Jakarta Timur', 'provinsi' => 'DKI Jakarta']);
        Sekolah::create(['kodlan' => 'BDG-KAB-SRN-001', 'namasekolah' => 'SMP Negeri 1 Soreang', 'jenjang' => 'SMP', 'status' => 'Negeri', 'kec' => 'Soreang', 'kotkab' => 'Kabupaten', 'kota' => 'Bandung', 'provinsi' => 'Jawa Barat']);
    }
}
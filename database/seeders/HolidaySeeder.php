<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    /**
     * Seed hari libur nasional Indonesia tahun 2026 dan 2027.
     *
     * 2026 — Sumber: SKB 3 Menteri Tahun 2025.
     * 2027 — Perkiraan berdasarkan kalender Hijriah, Saka, dan Gregorian.
     *         Wajib diperbarui saat SKB 3 Menteri 2027 resmi diterbitkan.
     */
    public function run(): void
    {
        $holidays = [
            // ══════════════════════════════════════════
            // TAHUN 2026
            // ══════════════════════════════════════════
            ['tanggal' => '2026-01-01', 'nama' => 'Tahun Baru Masehi 2026',                      'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-02-17', 'nama' => 'Tahun Baru Imlek 2577 Kongzili',              'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-03-03', 'nama' => 'Isra Mi\'raj Nabi Muhammad SAW 1447 H',      'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-03-19', 'nama' => 'Hari Suci Nyepi — Tahun Baru Saka 1948',     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-03-20', 'nama' => 'Idul Fitri 1447 Hijriah (Hari 1)',           'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-03-21', 'nama' => 'Idul Fitri 1447 Hijriah (Hari 2)',           'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-03-18', 'nama' => 'Cuti Bersama Idul Fitri 1447 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2026],
            ['tanggal' => '2026-03-23', 'nama' => 'Cuti Bersama Idul Fitri 1447 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2026],
            ['tanggal' => '2026-03-24', 'nama' => 'Cuti Bersama Idul Fitri 1447 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2026],
            ['tanggal' => '2026-04-03', 'nama' => 'Wafat Yesus Kristus (Good Friday)',          'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-05-01', 'nama' => 'Hari Buruh Internasional',                   'jenis' => 'hari_besar',     'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-05-14', 'nama' => 'Kenaikan Yesus Kristus',                     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-05-21', 'nama' => 'Hari Raya Waisak 2570 BE',                   'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-05-27', 'nama' => 'Idul Adha 1447 Hijriah',                     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-06-01', 'nama' => 'Hari Lahir Pancasila',                       'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-06-16', 'nama' => 'Tahun Baru Islam 1448 Hijriah',              'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia ke-81',  'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-08-25', 'nama' => 'Maulid Nabi Muhammad SAW 1448 H',            'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-12-25', 'nama' => 'Hari Raya Natal',                            'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2026],
            ['tanggal' => '2026-12-26', 'nama' => 'Cuti Bersama Hari Raya Natal',               'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2026],

            // ══════════════════════════════════════════
            // TAHUN 2027
            // ══════════════════════════════════════════
            // ⚠️ Catatan: Tanggal 2027 adalah perkiraan. Wajib diperbarui saat
            //    SKB 3 Menteri resmi diterbitkan oleh pemerintah.
            ['tanggal' => '2027-01-01', 'nama' => 'Tahun Baru Masehi 2027',                      'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tanggal pasti'],
            ['tanggal' => '2027-02-06', 'nama' => 'Tahun Baru Imlek 2578 Kongzili',              'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tahun Kambing'],
            ['tanggal' => '2027-02-18', 'nama' => 'Isra Mi\'raj Nabi Muhammad SAW 1448 H',      'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-03-09', 'nama' => 'Idul Fitri 1448 Hijriah (Hari 1)',           'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-03-10', 'nama' => 'Idul Fitri 1448 Hijriah (Hari 2)',           'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-03-08', 'nama' => 'Cuti Bersama Idul Fitri 1448 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2027, 'catatan' => 'Perkiraan'],
            ['tanggal' => '2027-03-11', 'nama' => 'Cuti Bersama Idul Fitri 1448 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2027, 'catatan' => 'Perkiraan'],
            ['tanggal' => '2027-03-12', 'nama' => 'Cuti Bersama Idul Fitri 1448 H',             'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2027, 'catatan' => 'Perkiraan'],
            ['tanggal' => '2027-03-07', 'nama' => 'Hari Suci Nyepi — Tahun Baru Saka 1949',     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-03-26', 'nama' => 'Wafat Yesus Kristus (Good Friday)',          'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Paskah 2027 = 28 Mar, Good Friday = 26 Mar'],
            ['tanggal' => '2027-05-01', 'nama' => 'Hari Buruh Internasional',                   'jenis' => 'hari_besar',     'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tanggal pasti'],
            ['tanggal' => '2027-05-13', 'nama' => 'Kenaikan Yesus Kristus',                     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => '39 hari setelah Paskah 28 Mar'],
            ['tanggal' => '2027-05-16', 'nama' => 'Idul Adha 1448 Hijriah',                     'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-06-01', 'nama' => 'Hari Lahir Pancasila',                       'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tanggal pasti'],
            ['tanggal' => '2027-06-02', 'nama' => 'Hari Raya Waisak 2571 BE',                   'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan bulan purnama'],
            ['tanggal' => '2027-06-05', 'nama' => 'Tahun Baru Islam 1449 Hijriah',              'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-08-14', 'nama' => 'Maulid Nabi Muhammad SAW 1449 H',            'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Perkiraan — konfirmasi SKB 3 Menteri'],
            ['tanggal' => '2027-08-17', 'nama' => 'Hari Kemerdekaan Republik Indonesia ke-82',  'jenis' => 'libur_nasional', 'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tanggal pasti'],
            ['tanggal' => '2027-12-25', 'nama' => 'Hari Raya Natal',                            'jenis' => 'libur_agama',    'is_tanggal_merah' => true,  'tahun' => 2027, 'catatan' => 'Tanggal pasti'],
            ['tanggal' => '2027-12-27', 'nama' => 'Cuti Bersama Hari Raya Natal',               'jenis' => 'cuti_bersama',   'is_tanggal_merah' => false, 'tahun' => 2027, 'catatan' => 'Perkiraan'],
        ];

        foreach ($holidays as $data) {
            Holiday::updateOrCreate(
                ['tanggal' => $data['tanggal']],
                [
                    'nama'             => $data['nama'],
                    'jenis'            => $data['jenis'],
                    'is_tanggal_merah' => $data['is_tanggal_merah'],
                    'tahun'            => $data['tahun'],
                    'catatan'          => $data['catatan'] ?? null,
                ]
            );
        }

        $this->command->info('✅ HolidaySeeder: ' . count($holidays) . ' hari libur nasional berhasil di-seed (2026–2027).');
    }
}

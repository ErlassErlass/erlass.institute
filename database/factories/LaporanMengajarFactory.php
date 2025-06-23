<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Siswa;
use App\Models\Absensi;
use App\Models\LaporanMengajar;

class LaporanMengajarFactory extends Factory
{
    public function definition(): array
    {
        dd('FILE FACTORY INI BERHASIL DIBACA');

        $instruktur = User::where('role', 'instruktur')->inRandomOrder()->first();
        $sekolah = Sekolah::inRandomOrder()->first();

        return [
            'user_id_instruktur' => $instruktur->id,
            'user_id_assisten' => User::where('role', 'instruktur')->where('id', '!=', $instruktur->id)->inRandomOrder()->first()->id,
            'sekolah_kodlan' => $sekolah->kodlan,
            'pertemuan_ke' => fake()->numberBetween(1, 16),
            'rombel' => fake()->numberBetween(1, 5),
            'jadwal_mengajar' => fake()->dateTimeThisYear(),
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '10:30:00',
            'materi_pengajaran' => fake()->paragraph(2),
            'refleksi_siswa' => fake()->paragraph(1),
            'refleksi_capaian' => fake()->paragraph(1),
            'keaktifan' => fake()->randomElement(['sangat_pasif', 'pasif', 'aktif', 'sangat_aktif']),
            'pemahaman_materi' => fake()->randomElement(['belum_paham', 'sedikit_paham', 'paham', 'sangat_paham']),

            // ✅ MENGGUNAKAN NAMA KOLOM YANG BENAR
            'jumlah_siswa_hadir' => 0,
            'jumlah_siswa_keluar' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (LaporanMengajar $laporan) {
            $students = Siswa::where('sekolah_kodlan', $laporan->sekolah_kodlan)
                ->inRandomOrder()
                ->limit(fake()->numberBetween(10, 25))
                ->get();

            if ($students->isEmpty()) {
                return;
            }

            foreach ($students as $student) {
                Absensi::factory()->create([
                    'laporan_mengajar_id' => $laporan->id,
                    'siswa_id' => $student->id,
                ]);
            }

            $laporan->jumlah_siswa_hadir = $laporan->absensi()->where('hadir', true)->count();

            // ✅ MENGGUNAKAN NAMA KOLOM YANG BENAR
            $laporan->jumlah_siswa_keluar = $laporan->absensi()->where('hadir', false)->count();

            $laporan->save();
        });
    }
}

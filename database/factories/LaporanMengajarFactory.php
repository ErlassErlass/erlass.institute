<?php

namespace Database\Factories;

use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanMengajarFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id_instruktur' => User::factory(),
            'user_id_assisten' => null,
            'pertemuan_ke' => $this->faker->numberBetween(1, 16),
            'rombel' => $this->faker->randomElement(['1', '2', '3', '4', '5']),
            'sekolah_kodlan' => Sekolah::factory(),
            'jadwal_mengajar' => Carbon::today()->subDays($this->faker->numberBetween(0, 7)),
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'kategori_pengajaran' => $this->faker->randomElement(['Regular', 'Remedial', 'Pengayaan']),
            'materi_pengajaran' => $this->faker->sentence(8),
            'jumlah_siswa_hadir' => 0,
            'jumlah_siswa_keluar' => 0,
            'jumlah_siswa_tidak_hadir' => 0,
            'foto_kegiatan' => null,
            'foto_absensi_siswa' => null,
            'refleksi_siswa' => $this->faker->paragraph(2),
            'refleksi_capaian' => $this->faker->paragraph(2),
            'keaktifan' => $this->faker->randomElement(['sangat_pasif', 'pasif', 'aktif', 'sangat_aktif']),
            'pemahaman_materi' => $this->faker->randomElement(['belum_paham', 'sedikit_paham', 'paham', 'sangat_paham']),
        ];
    }

    /**
     * Configure the factory to create related models
     */
    public function withInstructor(User $instructor): static
    {
        return $this->state(fn () => [
            'user_id_instruktur' => $instructor->id,
        ]);
    }

    /**
     * Configure the factory with a specific school
     */
    public function withSekolah(Sekolah $sekolah): static
    {
        return $this->state(fn () => [
            'sekolah_kodlan' => $sekolah->kodlan,
        ]);
    }

    /**
     * Configure the factory with assistant
     */
    public function withAssistant(User $assistant): static
    {
        return $this->state(fn () => [
            'user_id_assisten' => $assistant->id,
        ]);
    }

    /**
     * Configure the factory for a specific date
     */
    public function onDate(Carbon $date): static
    {
        return $this->state(fn () => [
            'jadwal_mengajar' => $date->format('Y-m-d'),
        ]);
    }

    /**
     * Configure the factory with file uploads
     */
    public function withFiles(): static
    {
        return $this->state(fn () => [
            'foto_kegiatan' => 'laporan_mengajar/sample_kegiatan.jpg',
            'foto_absensi_siswa' => 'laporan_mengajar_absensi/sample_absensi.jpg',
        ]);
    }
}

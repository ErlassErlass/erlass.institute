<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LaporanMengajar;
use App\Models\Siswa;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'laporan_mengajar_id' => LaporanMengajar::factory(),
            'siswa_id' => Siswa::factory(),
            'hadir' => fake()->boolean(85), // 85% kemungkinan hadir
        ];
    }

    /**
     * Create attendance record for specific laporan and siswa
     */
    public function forLaporanAndSiswa(LaporanMengajar $laporan, Siswa $siswa): static
    {
        return $this->state(fn () => [
            'laporan_mengajar_id' => $laporan->id,
            'siswa_id' => $siswa->id,
        ]);
    }

    /**
     * Create present attendance
     */
    public function present(): static
    {
        return $this->state(fn () => [
            'hadir' => true,
        ]);
    }

    /**
     * Create absent attendance
     */
    public function absent(): static
    {
        return $this->state(fn () => [
            'hadir' => false,
        ]);
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AbsensiFactory extends Factory
{
    public function definition(): array
    {
        return [
            // laporan_mengajar_id dan siswa_id akan diisi oleh LaporanMengajarFactory
            'hadir' => fake()->boolean(90), // 90% kemungkinan hadir
        ];
    }
}
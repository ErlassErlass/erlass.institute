<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Sekolah;

class SiswaFactory extends Factory
{
    // File: database/factories/SiswaFactory.php
public function definition(): array
{
    return [
        'nama_lengkap' => fake()->name(),
        'nisn' => fake()->unique()->numerify('##########'),
        'sekolah_kodlan' => Sekolah::inRandomOrder()->first()->kodlan,
        'rombel' => fake()->numberBetween(1, 5), // ✅ Diperbaiki
    ];
}
}
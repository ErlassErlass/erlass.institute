<?php

namespace Database\Factories;

use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

class SiswaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'nisn' => fake()->unique()->numerify('##########'),
            'sekolah_kodlan' => Sekolah::factory(),
            'rombel' => fake()->randomElement(['A1', 'A2', 'B1', 'B2', 'C1', 'C2']),
        ];
    }

    /**
     * Create student for specific school
     */
    public function forSekolah(Sekolah $sekolah): static
    {
        return $this->state(fn () => [
            'sekolah_kodlan' => $sekolah->kodlan,
        ]);
    }

    /**
     * Create student for specific rombel
     */
    public function inRombel(string $rombel): static
    {
        return $this->state(fn () => [
            'rombel' => $rombel,
        ]);
    }

    /**
     * Create student with specific NISN
     */
    public function withNisn(string $nisn): static
    {
        return $this->state(fn () => [
            'nisn' => $nisn,
        ]);
    }
}

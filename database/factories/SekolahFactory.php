<?php

// database/factories/SekolahFactory.php
namespace Database\Factories;

use App\Models\Sekolah;
use Faker\Generator as Faker;

class SekolahFactory extends Factory
{
    protected $model = Sekolah::class;

    public function definition()
    {
        return [
            'kodlan' => $this->faker->unique()->numerify('SCH-####'), // e.g., SCH-1234
            'namasekolah' => $this->faker->company() . ' ' . $this->faker->randomElement(['SD', 'SMP']),
            'rank' => $this->faker->optional($weight = 0.5)->randomDigit,
            'jenjang' => $this->faker->randomElement(['SD', 'SMP']),
            'sub_jenjang' => $this->faker->optional()->word,
            'status' => $this->faker->randomElement(['Swasta', 'Negeri']),
            'pd' => $this->faker->optional()->word,
            'kec' => $this->faker->citySuffix, // e.g., "Kecamatan"
            'kotkab' => $this->faker->city, // e.g., "Jakarta Selatan"
            'kota' => $this->faker->city, // e.g., "Jakarta"
            'provinsi' => $this->faker->randomElement(['Jawa Barat', 'Jawa Tengah', 'DKI Jakarta', 'Banten']),
        ];
    }
}
<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SekolahFactory extends Factory
{
    public function definition(): array
    {
        $provinsi = fake()->randomElement(['DKI Jakarta', 'Jawa Barat', 'Jawa Tengah']);
        $kotkab = fake()->randomElement(['Kota', 'Kabupaten']);
        $kota = ($provinsi == 'DKI Jakarta') ? fake()->randomElement(['Jakarta Selatan', 'Jakarta Timur', 'Jakarta Pusat']) : fake()->city();
        $kec = fake()->streetName();
        $jenjang = fake()->randomElement(['SD', 'SMP']);
        $namaSekolah = $jenjang . ' Negeri ' . fake()->numberBetween(1, 20) . ' ' . $kec;

        return [
            'kodlan' => strtoupper(substr($provinsi, 0, 3)) . '-' . strtoupper(substr($kota, 0, 3)) . '-' . fake()->unique()->numerify('###'),
            'namasekolah' => $namaSekolah,
            'jenjang' => $jenjang,
            'status' => fake()->randomElement(['Negeri', 'Swasta']),
            'pd' => fake()->numberBetween(150, 500),
            'kec' => $kec,
            'kotkab' => $kotkab,
            'kota' => $kota,
            'provinsi' => $provinsi,
        ];
    }
}
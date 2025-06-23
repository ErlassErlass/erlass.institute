<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama_lengkap' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'), // Default password
            'tanggal_lahir' => fake()->date(),
            'no_telephone' => fake()->phoneNumber(),
            'status' => 'Aktif',
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'pend_terakhir' => 'S1',
            'kompetensi_1' => fake()->randomElement(['Coding', 'Robotik', 'Desain', 'IoT']),
            'role' => 'instruktur', // Default role
        ];
    }
}
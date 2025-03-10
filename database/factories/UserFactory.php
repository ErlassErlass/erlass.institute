<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory {
    protected $model = User::class;

    public function definition(): array {
        return [
            'nama_lengkap' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => bcrypt('password'), // Default password
            'tanggal_lahir' => $this->faker->date,
            'no_telephone' => $this->faker->phoneNumber,
            'status' => 'active',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Coding Scratch',
            'kompetensi_2' => 'Arduino Learning Kit',
            'role' => 'instruktur',
        ];
    }
}
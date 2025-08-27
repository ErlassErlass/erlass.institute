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
            'tanggal_lahir' => fake()->date('Y-m-d', '-25 years'),
            'no_telephone' => fake()->numerify('08##########'),
            'status' => 'Aktif',
            'agama' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Lainnya']),
            'pend_terakhir' => fake()->randomElement(['SMA', 'D3', 'S1', 'S2', 'S3']),
            'kompetensi_1' => fake()->randomElement(['Coding', 'Robotik', 'Desain', 'IoT', 'Data Science']),
            'kompetensi_2' => fake()->optional()->randomElement(['Coding', 'Robotik', 'Desain', 'IoT', 'Data Science']),
            'role' => 'instruktur', // Default role
            // Field untuk sistem verifikasi instruktur
            'is_verified' => false,
            'verification_status' => 'pending', // pending, approved, rejected
            'verified_at' => null,
            'verified_by' => null,
            'rejection_reason' => null,
            'verification_documents' => null, // JSON field untuk menyimpan path dokumen
            'application_date' => now(),
        ];
    }

    /**
     * Create an instructor user
     */
    public function instructor(): static
    {
        return $this->state(fn () => [
            'role' => 'instruktur',
        ]);
    }

    /**
     * Create a webmaster user (highest access)
     */
    public function webmaster(): static
    {
        return $this->state(fn () => [
            'role' => 'webmaster',
            'nama_lengkap' => 'Webmaster',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);
    }

    /**
     * Create a debug user
     */
    public function debugUser(): static
    {
        return $this->state(fn () => [
            'role' => 'debug_user',
            'nama_lengkap' => 'Debug User',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => now(),
        ]);
    }

    /**
     * Create an admin erlass user
     */
    public function adminErlass(): static
    {
        return $this->state(fn () => [
            'role' => 'admin_erlass',
            'nama_lengkap' => 'Admin ERLASS',
        ]);
    }

    /**
     * Create verified instructor
     */
    public function verifiedInstructor(): static
    {
        return $this->state(fn () => [
            'role' => 'instruktur',
            'is_verified' => true,
            'verification_status' => 'approved',
            'verified_at' => fake()->dateTimeBetween('-6 months', 'now'),
            'verified_by' => 1, // Assume verified by first webmaster
        ]);
    }

    /**
     * Create pending instructor
     */
    public function pendingInstructor(): static
    {
        return $this->state(fn () => [
            'role' => 'instruktur',
            'is_verified' => false,
            'verification_status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'application_date' => fake()->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    /**
     * Create rejected instructor
     */
    public function rejectedInstructor(): static
    {
        return $this->state(fn () => [
            'role' => 'instruktur',
            'is_verified' => false,
            'verification_status' => 'rejected',
            'verified_at' => null,
            'verified_by' => 1,
            'rejection_reason' => fake()->randomElement([
                'Dokumen tidak lengkap',
                'Kualifikasi tidak memenuhi syarat',
                'Data tidak valid',
                'Dokumen tidak dapat diverifikasi'
            ]),
        ]);
    }

    /**
     * Create user with specific email
     */
    public function withEmail(string $email): static
    {
        return $this->state(fn () => [
            'email' => $email,
        ]);
    }
}
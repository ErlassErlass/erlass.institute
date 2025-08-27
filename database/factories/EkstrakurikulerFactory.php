<?php

namespace Database\Factories;

use App\Models\Ekstrakurikuler;
use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ekstrakurikuler>
 */
class EkstrakurikulerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ekstrakurikuler::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kategoriProgram = $this->faker->randomElement([
            'Coding Scratch', 
            'English Course', 
            'Micro:bit Learning Kit', 
            'Pictoblox AI', 
            'Robotik Explorer', 
            'Robotik Jimu'
        ]);

        $cities = [
            'Jakarta Pusat', 'Jakarta Utara', 'Jakarta Selatan', 'Jakarta Timur', 'Jakarta Barat',
            'Kota Depok', 'Kota Bogor', 'Kota Tangerang', 'Kota Bekasi'
        ];

        $city = $this->faker->randomElement($cities);
        $region = $this->getCityRegion($city);

        $startDate = $this->faker->dateTimeBetween('now', '+1 month');
        $endDate = $this->faker->dateTimeBetween($startDate, '+6 months');

        return [
            'kategori_program' => $kategoriProgram,
            'deskripsi' => $this->faker->optional()->paragraph(),
            'user_id_sales' => User::factory(),
            'region' => $region,
            'city' => $city,
            'sekolah_kodlan' => Sekolah::factory(),
            'alamat_lengkap' => $this->faker->address(),
            'google_maps_link' => $this->faker->optional()->url(),
            'jarak_km' => $this->faker->numberBetween(1, 50),
            'kepala_sekolah' => $this->faker->name(),
            'penanggung_jawab' => $this->faker->name(),
            'no_telepon' => $this->faker->phoneNumber(),
            'email' => $this->faker->optional()->email(),
            'koneksi_internet' => $this->faker->randomElement(['ada', 'tidak_ada', 'tidak_diketahui']),
            'proyektor' => $this->faker->randomElement(['ada', 'tidak_ada', 'tidak_diketahui']),
            'keterangan_proyektor' => $this->faker->optional()->sentence(),
            'kabel_hdmi' => $this->faker->randomElement(['ada', 'tidak_ada', 'tidak_diketahui']),
            'kabel_vga' => $this->faker->randomElement(['ada', 'tidak_ada', 'tidak_diketahui']),
            'keterangan_kabel' => $this->faker->optional()->sentence(),
            'total_siswa' => $this->faker->numberBetween(10, 100),
            'total_ruangan' => $this->faker->numberBetween(1, 5),
            'total_rombel' => $this->faker->numberBetween(1, 5),
            'tanggal_mulai' => $startDate,
            'tanggal_selesai' => $endDate,
            'total_pertemuan' => $this->faker->numberBetween(8, 24),
            'frekuensi' => Ekstrakurikuler::FREKUENSI_MINGGUAN,
            'status' => $this->faker->randomElement([
                Ekstrakurikuler::STATUS_DRAFT,
                Ekstrakurikuler::STATUS_DIAJUKAN,
                Ekstrakurikuler::STATUS_DISETUJUI,
                Ekstrakurikuler::STATUS_AKTIF,
            ]),
            'created_by' => User::factory(),
            'updated_by' => User::factory(),
        ];
    }

    /**
     * Get region based on city
     */
    private function getCityRegion(string $city): string
    {
        $cityToRegionMap = [
            'Jakarta Pusat' => 'JAKARTA',
            'Jakarta Utara' => 'JAKARTA', 
            'Jakarta Selatan' => 'JAKARTA',
            'Jakarta Timur' => 'JAKARTA',
            'Jakarta Barat' => 'JAKARTA',
            'Kota Depok' => 'DEPOK',
            'Kota Bogor' => 'BOGOR',
            'Kota Tangerang' => 'TANGERANG',
            'Kota Bekasi' => 'BEKASI'
        ];

        return $cityToRegionMap[$city] ?? strtoupper(explode(' ', $city)[1] ?? $city);
    }

    /**
     * Indicate that the ekstrakurikuler is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_DRAFT,
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is submitted for approval.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_DIAJUKAN,
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_DISETUJUI,
            'tanggal_disetujui' => now(),
            'disetujui_oleh' => User::factory(),
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_AKTIF,
            'tanggal_disetujui' => now()->subDays(5),
            'disetujui_oleh' => User::factory(),
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_SELESAI,
            'tanggal_disetujui' => now()->subDays(90),
            'disetujui_oleh' => User::factory(),
            'tanggal_mulai' => now()->subDays(90),
            'tanggal_selesai' => now()->subDays(1),
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_DIBATALKAN,
        ]);
    }

    /**
     * Indicate that the ekstrakurikuler is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Ekstrakurikuler::STATUS_DITOLAK,
        ]);
    }
}
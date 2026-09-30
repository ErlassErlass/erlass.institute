<?php

namespace Database\Factories;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EkstrakurikulerRombel>
 */
class EkstrakurikulerRombelFactory extends Factory
{
    protected $model = EkstrakurikulerRombel::class;

    public function definition(): array
    {
        $instruktur = User::factory()->create(['role' => 'instruktur']);
        $ekskul     = Ekstrakurikuler::factory()->create();

        return [
            'ekstrakurikuler_id'   => $ekskul->id,
            'nama_rombel'          => 'Rombel ' . $this->faker->randomLetter() . $this->faker->numberBetween(1, 9),
            'nomor_rombel'         => $this->faker->numberBetween(1, 10),
            'jumlah_siswa'         => $this->faker->numberBetween(10, 35),
            'ruangan'              => 'Kelas ' . $this->faker->randomLetter(),
            'tanggal_mulai'        => now()->subMonths(2),
            'tanggal_selesai'      => now()->addMonths(8),
            'hari'                 => $this->faker->randomElement(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']),
            'jam_mulai'            => '14:00',
            'jam_selesai'          => '15:30',
            'total_pertemuan'      => 32,
            'frekuensi'            => 'mingguan',
            'pertemuan_selesai'    => $this->faker->numberBetween(0, 16),
            'user_id_instruktur'   => $instruktur->id,
            'status'               => 'berlangsung',
        ];
    }
}

<?php

namespace Database\Factories;

use App\Models\EkstrakurikulerRombel;
use App\Models\InvoiceApproval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InvoiceApproval>
 */
class InvoiceApprovalFactory extends Factory
{
    protected $model = InvoiceApproval::class;

    public function definition(): array
    {
        $tahunAjaran = '2026/2027';
        $skema       = $this->faker->randomElement([
            'bulanan', 'semester', 'tahunan', 'per_4_pertemuan',
        ]);

        $periodeLabel = match ($skema) {
            'bulanan'         => $this->faker->randomElement(['Agustus 2026', 'September 2026', 'Oktober 2026']),
            'semester'        => $this->faker->randomElement(['Semester 1 2026/2027', 'Semester 2 2026/2027']),
            'tahunan'         => 'Tahun Ajaran 2026/2027',
            'per_4_pertemuan' => 'Batch ' . $this->faker->numberBetween(1, 8),
        };

        // Buat rombel dummy via factory chain
        $rombel = EkstrakurikulerRombel::factory()->create();

        return [
            'ekstrakurikuler_rombel_id' => $rombel->id,
            'skema_tagihan'             => $skema,
            'periode_label'             => $periodeLabel,
            'tahun_ajaran'              => $tahunAjaran,
            'periode_nomor'             => $this->faker->numberBetween(1, 8),
            'sesi_dari'                 => 1,
            'sesi_sampai'               => 4,
            'jumlah_siswa_billable'     => $this->faker->numberBetween(10, 35),
            'jumlah_sesi'               => $this->faker->randomElement([4, 8, 16, 32]),
            'nomor_invoice'             => 'INV/ERLASS/' . now()->format('Ym') . '/TEST/' . $this->faker->unique()->numerify('###'),
            'status'                    => 'pending_operasional',
            'operasional_status'        => 'pending',
            'akunting_status'           => 'pending',
            'koreksi_siswa_billable'    => null,
            'koreksi_catatan'           => null,
            'koreksi_by'                => null,
            'koreksi_at'                => null,
            'created_by'                => null,
        ];
    }

    /**
     * State: Invoice sudah fully approved.
     */
    public function approved(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create();
            return [
                'status'                 => 'approved',
                'operasional_status'     => 'approved',
                'operasional_user_id'    => $user->id,
                'operasional_approved_at'=> now()->subHour(),
                'akunting_status'        => 'approved',
                'akunting_user_id'       => $user->id,
                'akunting_approved_at'   => now(),
            ];
        });
    }

    /**
     * State: Menunggu approval akunting.
     */
    public function pendingAkunting(): static
    {
        return $this->state(function (array $attributes) {
            $user = User::factory()->create();
            return [
                'status'                 => 'pending_akunting',
                'operasional_status'     => 'approved',
                'operasional_user_id'    => $user->id,
                'operasional_approved_at'=> now()->subMinutes(30),
                'akunting_status'        => 'pending',
            ];
        });
    }

    /**
     * State: Ada koreksi billable aktif.
     */
    public function withKoreksi(int $nilai = 15, string $catatan = 'Koreksi test default'): static
    {
        return $this->state(function (array $attributes) use ($nilai, $catatan) {
            return [
                'koreksi_siswa_billable' => $nilai,
                'koreksi_catatan'        => $catatan,
                'koreksi_at'             => now(),
            ];
        });
    }
}

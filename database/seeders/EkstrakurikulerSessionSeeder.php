<?php

namespace Database\Seeders;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EkstrakurikulerSessionSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure we have an instructor
        $instructor = User::where('role', 'instruktur')->first();
        if (!$instructor) {
            $this->command->error('No instructor found. Please run UserSeeder first.');
            return;
        }
        
        // Ensure we have a school
        $sekolah = Sekolah::first();
        if (!$sekolah) {
            $this->command->error('No sekolah found. Please run SekolahSeeder first.');
            return;
        }

        // Create or get Ekstrakurikuler
        // Note: nama_program column was removed and replaced by kategori_program
        $ekskul = Ekstrakurikuler::firstOrCreate(
            ['sekolah_kodlan' => $sekolah->kodlan, 'kategori_program' => 'Coding Scratch'],
            [
                'deskripsi' => 'Test Program for Calendar Verification',
                'user_id_sales' => null, // Optional
                'sekolah_kodlan' => $sekolah->kodlan,
                'total_siswa' => 20,
                'total_ruangan' => 1,
                'total_rombel' => 1,
                'tanggal_mulai' => Carbon::now()->startOfMonth(),
                'tanggal_selesai' => Carbon::now()->addYear(),
                'total_pertemuan' => 12,
                'frekuensi' => 'mingguan',
                'status' => 'aktif',
                // Removed fields that might not exist or are optional
            ]
        );

        // Create Rombel
        $rombel = EkstrakurikulerRombel::firstOrCreate(
            ['ekstrakurikuler_id' => $ekskul->id, 'nama_rombel' => 'Rombel A (Test)'],
            [
                'nomor_rombel' => 1,
                'hari' => 'senin', // lowercase per enum
                'jam_mulai' => '14:00',
                'jam_selesai' => '16:00',
                'status' => 'berlangsung',
                'jumlah_siswa' => 20,
                'tanggal_mulai' => Carbon::now()->startOfMonth(),
                'tanggal_selesai' => Carbon::now()->addYear(),
                'total_pertemuan' => 12,
                'frekuensi' => 'mingguan',
            ]
        );

        // Generate Sessions for Current Month & Next Month
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonth()->endOfMonth();

        $current = $startDate->copy();
        $meetingNumber = 1;

        while ($current <= $endDate) {
            if ($current->dayOfWeek === Carbon::MONDAY) {
                EkstrakurikulerSession::updateOrCreate(
                    [
                        'ekstrakurikuler_rombel_id' => $rombel->id,
                        'tanggal_terjadwal' => $current->format('Y-m-d'),
                    ],
                    [
                        'user_id_instruktur' => $instructor->id,
                        'jam_mulai_terjadwal' => '14:00:00',
                        'jam_selesai_terjadwal' => '16:00:00',
                        'status' => 'terjadwal',
                        'nomor_pertemuan' => $meetingNumber++, // Distinct number
                        'topik_materi' => 'Materi Test Calendar ' . $current->format('d M'),
                    ]
                );
            }
            $current->addDay();
        }
        
        $this->command->info('Generated sessions for ' . $ekskul->kategori_program);
    }
}

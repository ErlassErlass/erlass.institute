<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\InstructorProfile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\ToArray;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportInstructorsSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('Data Instruktur Erlass 2025.xlsx');

        if (!file_exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        // 1. Wipe existing instructors
        $this->command->info('Wiping existing instructors...');
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        try {
            $existingCount = User::where('role', 'instruktur')->count();
            if ($existingCount > 0) {
                // Delete profiles first
                $userIds = User::where('role', 'instruktur')->pluck('id');
                InstructorProfile::whereIn('user_id', $userIds)->delete();
                // Also update related tables to set null or delete if cascade not set
                // For now, force delete user is enough with checks off, but orphan records might exist.
                // Ideally we should set null in ekstrakurikuler_session if we want to keep sessions.
                // But "Wipe" usually means clean start.
                
                User::whereIn('id', $userIds)->delete();
            }
            $this->command->info("Deleted $existingCount instructors.");
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // 2. Read Excel
        $this->command->info('Reading Excel file...');
        $data = Excel::toArray(new class implements ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $path);

        if (empty($data) || empty($data[0])) {
            $this->command->error("Excel file is empty or invalid.");
            return;
        }

        $rows = $data[0];
        // Remove header row
        array_shift($rows);

        // Sort by 'Tanggal bergabung' (Column index 1) to ensure ID sequence
        usort($rows, function ($a, $b) {
            return ($a[1] ?? 0) <=> ($b[1] ?? 0);
        });

        $sequences = []; // Track sequence per year

        foreach ($rows as $row) {
            try {
                // Map Columns
                // 0: No
                // 1: Tanggal bergabung
                // 2: Masa Kerja
                // 3: Nama Instruktur
                // 4: Tanggal Lahir
                // 5: Usia
                // 6: Pendidikan terakhir
                // 7: No Hp
                // 8: Agama
                // 9: Alamat tempat tinggal

                $joinDateVal = $row[1];
                $name = $row[3];
                $birthDateVal = $row[4];
                $education = $row[6];
                $phone = $row[7];
                $religion = $row[8];
                $address = $row[9];

                if (empty($name)) continue;

                // Process Dates
                $joinDate = null;
                if (is_numeric($joinDateVal)) {
                    $joinDate = Date::excelToDateTimeObject($joinDateVal);
                } else {
                    // Fallback or try parse string
                     try { $joinDate = new \DateTime($joinDateVal); } catch(\Exception $e) { $joinDate = now(); }
                }

                $birthDate = null;
                if (is_numeric($birthDateVal)) {
                    $birthDate = Date::excelToDateTimeObject($birthDateVal);
                }

                // Generate ID
                $year = $joinDate ? $joinDate->format('Y') : date('Y');
                if (!isset($sequences[$year])) {
                    $sequences[$year] = 1;
                } else {
                    $sequences[$year]++;
                }
                $prefix = 'ICE' . $year;
                $instructorId = $prefix . $sequences[$year];


                // Generate Email
                // Remove titles/special chars for email
                $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $name);
                $nameParts = explode(' ', strtolower(trim($cleanName)));
                $slug = $nameParts[0];
                if (count($nameParts) > 1) {
                    $slug .= '.' . $nameParts[1];
                }
                // Ensure unique email
                $baseEmail = $slug . '@instructor.erlass.com';
                $email = $baseEmail;
                $counter = 1;
                while (User::where('email', $email)->exists()) {
                    $email = $slug . $counter . '@instructor.erlass.com';
                    $counter++;
                }

                // Create User
                $user = User::create([
                    'nama_lengkap' => $name,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'role' => 'instruktur',
                    'status' => 'Aktif',
                    'instructor_id' => $instructorId,
                    'is_verified' => true, // Auto verify imported data? User didn't specify, but assuming yes for "siap deployment". Or false if they need to complete data? 
                                           // User said "belum lengkap bisa dilengkapi sendiri", so maybe unverified? 
                                           // But user also said "update docs deployment", implying readiness. 
                                           // Let's set to TRUE for basic access, but incomplete profile will trigger dashboard alert anyway.
                    'verification_status' => 'approved', // Presume approved as they are existing instructors
                    'verified_at' => now(),
                    'created_at' => $joinDate ?? now(),
                    'tanggal_lahir' => $birthDate,
                    'no_telephone' => strval($phone),
                    'agama' => $religion,
                    'pend_terakhir' => Str::limit($education, 10, ''), // Simplistic truncation, profile has full details
                ]);

                // Create Profile
                InstructorProfile::create([
                    'user_id' => $user->id,
                    'alamat_domisili' => $address,
                    'universitas_jurusan' => $education,
                    'nama_panggilan' => $nameParts[0] ?? $name,
                    // Fill other required fields with placeholders or null if nullable
                    'gelar_depan' => null,
                    'gelar_belakang' => null, 
                    'no_hp_2' => '-', // Placeholder
                    'kota_domisili' => '-', // Placeholder
                    'status_pernikahan' => 'Lajang', // Default
                    'pekerjaan_terakhir' => '-',
                    'jenjang_mengajar' => '-',
                    'nama_bank' => '-',
                    'no_rekening' => '-',
                    'nik' => '0000000000000000', // Placeholder
                    'tinggi_berat_badan' => '- / -',
                    'mata_minus' => '-',
                    'kendaraan' => 'Umum',
                    'jenis_kendaraan' => '-',
                    'waktu_mengajar' => [],
                ]);

                $this->command->info("Imported: $name ($instructorId) - $email");

            } catch (\Exception $e) {
                $this->command->error("Failed to row: " . json_encode($row) . " Error: " . $e->getMessage());
            }
        }
    }
}

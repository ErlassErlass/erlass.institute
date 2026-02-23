<?php

namespace Database\Seeders;

use App\Models\Sekolah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SekolahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('database/data/DataSekolah.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File DataSekolah.csv not found at: $csvFile");
            return;
        }

        // Open file in read mode
        $handle = fopen($csvFile, 'r');
        
        if (!$handle) {
            $this->command->error("Failed to open file: $csvFile");
            return;
        }

        $header = fgetcsv($handle, 1000, ','); // Read header row
        // Headers: KodLan,NamSek,Rank,Jenjang,SubJenjang,Status,PD,Kec,KotKab,Kota,Provinsi,Alamat
        
        $row = 0;
        $success = 0;
        $validKodlans = [];

        DB::beginTransaction();
        
        try {
            while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                $row++;
                
                // Skip if KodLan is empty
                if (empty($data[0])) {
                    continue;
                }

                // Map data based on CSV Index
                $provinsi = trim($data[10]);
                $namaSekolah = trim($data[1]);
                $jenjang = trim($data[3]);
                $status = trim($data[5]);

                // Filter: Remove Lampung
                if ($provinsi === 'Lampung') {
                    continue;
                }

                // Filter: Remove Perorangan
                if ($jenjang === 'Peror' || $status === 'Peror' || str_contains($namaSekolah, 'PERORANGAN')) {
                    continue;
                }
                
                // DATA CLEANING:
                // 1. Skip summary rows (starts with 'Tot')
                // 2. Skip invalid rows where school name is empty (fix for duplicate empty lines like kodlan 20110034)
                if (str_starts_with($data[0], 'Tot') || empty($namaSekolah)) {
                    continue;
                }

                $schoolData = [
                    'kodlan' => trim($data[0]),
                    'namasekolah' => $namaSekolah,
                    'rank' => !empty($data[2]) ? trim($data[2]) : null,
                    'jenjang' => trim($data[3]),
                    'sub_jenjang' => !empty($data[4]) ? trim($data[4]) : null,
                    'status' => trim($data[5]),
                    'pd' => !empty($data[6]) ? trim($data[6]) : null,
                    'kec' => trim($data[7]),
                    'kotkab' => trim($data[8]),
                    'kota' => trim($data[9]),
                    'provinsi' => $provinsi,
                    // Add Alamat if schema allows (we just added it)
                    'alamat' => isset($data[11]) ? trim($data[11]) : null,
                ];

                Sekolah::updateOrCreate(
                    ['kodlan' => $schoolData['kodlan']],
                    $schoolData
                );
                
                $success++;
                
                // Show progress every 500 rows
                if ($row % 500 == 0) {
                    $this->command->info("Processed $row rows...");
                }

                // Collect valid IDs
                $validKodlans[] = $schoolData['kodlan'];
            }

            // Explicitly delete Lampung and Peror records (User Request)
            Sekolah::where('provinsi', 'Lampung')->delete();
            Sekolah::where(function($q) {
                 $q->where('jenjang', 'Peror')
                   ->orWhere('status', 'Peror')
                   ->orWhere('namasekolah', 'LIKE', '%PERORANGAN%');
            })->delete();

            // Sync: Delete schools that are NOT in the CSV
            if (!empty($validKodlans)) {
                try {
                    $deleted = Sekolah::whereNotIn('kodlan', $validKodlans)->delete();
                    $this->command->info("Deleted $deleted schools that were not in the CSV.");
                } catch (\Exception $e) {
                    $this->command->warn("Could not delete some schools due to foreign key constraints (Siswa table). This is expected if schools have students.");
                    Log::warning("Sekolah Cleanup Failed: " . $e->getMessage());
                }
            }
            
            DB::commit();
            $this->command->info("Successfully imported $success schools from $row rows.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error("Error importing data at row $row: " . $e->getMessage());
            Log::error("Sekolah Import Error: " . $e->getMessage());
        } finally {
            fclose($handle);
            // Free up memory
            unset($validKodlans);
        }
    }
}

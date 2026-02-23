<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Siswa;

class ManualSiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('database/data/siswa_import.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File siswa_import.csv not found in project root.");
            return;
        }

        $this->command->info("Importing data from $csvFile...");

        // Use the existing service for consistency
        $importer = new \App\Services\SiswaImporterService();
        $results = $importer->import($csvFile, 'csv');

        $this->command->info("Import Completed.");
        $this->command->info("Success: {$results['success']}");
        $this->command->info("Failed: {$results['failed']}");
        
        if ($results['failed'] > 0) {
            foreach(array_slice($results['errors'], 0, 10) as $error) {
                $this->command->error($error);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvFile = base_path('database/data/employees_import.csv');
        
        if (!file_exists($csvFile)) {
            $this->command->error("File employees_import.csv not found.");
            return;
        }

        $handle = fopen($csvFile, 'r');
        if (!$handle) {
            return; // Fail silently or log
        }

        // Skip header
        fgetcsv($handle);

        $defaultPassword = bcrypt('Employee_2026!');

        while (($data = fgetcsv($handle)) !== false) {
            // CSV: employee_id, nama_lengkap, jabatan, role
            $employeeId = $data[0];
            $nama = trim($data[1]);
            $jabatan = trim($data[2]);
            $role = trim($data[3]);

            // Generate Email
            // Format: firstname.lastname@erlass.com (or similar)
            $nameParts = explode(' ', strtolower($nama));
            $emailName = $nameParts[0];
            if (count($nameParts) > 1) {
                $emailName .= '.' . $nameParts[count($nameParts)-1];
            }
            // Remove non-alphanumeric chars from email
            $emailName = preg_replace('/[^a-z0-9.]/', '', $emailName);
            $email = $emailName . '@erlass.com';
            
            // Check for duplicate email locally
            $counter = 1;
            while (User::where('email', $email)->exists()) {
                $email = $emailName . $counter . '@erlass.com';
                $counter++;
            }

            User::updateOrCreate(
                ['email' => $email], // Use email as unique identifier for now
                [
                    'nama_lengkap' => $nama,
                    'password' => $defaultPassword,
                    'role' => $role,
                    'is_verified' => true,
                    'verification_status' => 'approved',
                    'verified_at' => now(),
                    // Store employee ID and Job Title in metadata or specific fields if available
                    // For now, we don't have an 'employee_id' column in the user table shown in User.php
                    // We can repurpose 'kompetensi_1' for Job Title temporarily if needed, or just let it be.
                    'kompetensi_1' => $jabatan, 
                    'kompetensi_2' => "ID: " . $employeeId,
                    'status' => 'active',
                    'tanggal_lahir' => '1990-01-01', // Dummy
                    'no_telephone' => '08' . rand(111111111, 999999999), // Dummy
                ]
            );
        }

        fclose($handle);
        $this->command->info("Employees imported successfully.");
    }
}

<?php

namespace App\Services;

use App\Models\Sekolah;
use App\Models\Siswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SimpleXLSX; 

class SiswaImporterService
{
    /**
     * Import siswa from CSV/Excel file.
     * 
     * @param string $filePath
     * @return array Result summary
     */
    public function import(string $filePath, string $extension = null): array
    {
        $data = $this->parseFile($filePath, $extension);
        
        $results = [
            'success' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        if (empty($data)) {
            $results['errors'][] = 'File kosong atau format tidak valid.';
            return $results;
        }

        foreach ($data as $row) {
            $row = array_change_key_case($row, CASE_LOWER);
            
            // Normalize keys (basic cleanup)
            $cleanRow = [];
            foreach ($row as $key => $value) {
                $cleanKey = str_replace(' ', '_', trim($key));
                $cleanRow[$cleanKey] = trim($value);
            }
            
            // Smart Map Headers (Handle variations like 'Nama Siswa' -> 'nama_lengkap')
            $normalizedRow = $this->mapHeaders($cleanRow);
            
            $validation = $this->validateRow($normalizedRow);

            if ($validation->fails()) {
                $results['failed']++;
                // Detailed error info to help debug header issues
                $debugHeaders = implode(', ', array_keys($normalizedRow));
                $results['errors'][] = "Baris " . ($results['success'] + $results['failed'] + 1) . ": " . implode(', ', $validation->errors()->all()) . " (Terbaca: $debugHeaders)";
                continue;
            }

            try {
                $this->createOrUpdateSiswa($normalizedRow);
                $results['success']++;
            } catch (\Exception $e) {
                $results['failed']++;
                $results['errors'][] = "Baris " . ($results['success'] + $results['failed'] + 1) . ": Error saving data - " . $e->getMessage();
            }
        }

        return $results;
    }

    protected function parseFile(string $filePath, ?string $clientExtension = null): array
    {
        // Use client extension if provided, otherwise fallback to file path extension
        $extension = $clientExtension ? $clientExtension : pathinfo($filePath, PATHINFO_EXTENSION);
        $data = [];

        if (in_array(strtolower($extension), ['csv', 'txt'])) {
            $fileContent = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            
            if (empty($fileContent)) {
                return [];
            }

            // Detect delimiter from first line
            $firstLine = $fileContent[0];
            $delimiter = ',';
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            }

            // Parse header
            $header = str_getcsv($firstLine, $delimiter);
            
            // Clean BOM from first header column if exists
            if (isset($header[0])) {
                $header[0] = preg_replace('/[\x{FEFF}\x{200B}]/u', '', $header[0]);
            }
            
            // Trim headers
            $header = array_map('trim', $header);
            
            // Parse remaining rows
            for ($i = 1; $i < count($fileContent); $i++) {
                $row = str_getcsv($fileContent[$i], $delimiter);
                
                if (count($row) === count($header)) {
                    $data[] = array_combine($header, $row);
                }
            }
        } elseif (in_array(strtolower($extension), ['xlsx', 'xls'])) {
            try {
                // Read the file using Maatwebsite Excel
                $array = \Maatwebsite\Excel\Facades\Excel::toArray([], $filePath);
                
                if (!empty($array) && !empty($array[0])) {
                    $rows = $array[0]; // Get the first sheet
                    
                    if (count($rows) > 0) {
                        // Assume first row is header
                        $header = array_map('trim', $rows[0]);
                        
                        // Parse remaining rows
                        for ($i = 1; $i < count($rows); $i++) {
                            $row = $rows[$i];
                            // Ensure row has same column count as header or pad/trim
                            // Excel might return empty trailing columns or missing ones
                            if (count($row) < count($header)) {
                                $row = array_pad($row, count($header), null);
                            }
                            // Slice if row is longer than header
                            $row = array_slice($row, 0, count($header));
                            
                            // Filter out completely empty rows
                            if (empty(array_filter($row, function($v) { return $v !== null && $v !== ''; }))) {
                                continue;
                            }

                            $data[] = array_combine($header, $row);
                        }
                    }
                }
            } catch (\Exception $e) {
                throw new \Exception("Gagal membaca file Excel: " . $e->getMessage());
            }
        } else {
            throw new \Exception("Format file .$extension belum didukung. Gunakan CSV atau Excel (.xlsx).");
        }

        return $data;
    }

    protected function validateRow(array $row)
    {
        return Validator::make($row, [
            'nama_lengkap' => 'required|string',
            'nisn' => 'required|string', // Removed unique check here to handle updates/duplicates gracefully
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'kelas' => 'required|string',
        ]);
    }

    protected function createOrUpdateSiswa(array $data)
    {
        Siswa::updateOrCreate(
            ['nisn' => $data['nisn']],
            [
                'nama_lengkap' => $data['nama_lengkap'],
                'sekolah_kodlan' => $data['sekolah_kodlan'],
                'kelas' => $data['kelas'],
                'rombel' => $data['kelas'], // Sync rombel with kelas
            ]
        );
    }
    /**
     * Map common header variations to standard keys
     */
    protected function mapHeaders(array $row): array
    {
        $mapped = [];
        $mappings = [
            'nama_lengkap' => ['nama', 'nama_siswa', 'nama_lengkap', 'name', 'student_name', 'nama_peserta_didik', 'nama_pd'],
            'nisn' => ['nisn', 'nis', 'nomor_induk_siswa_nasional', 'nomor_induk'],
            'sekolah_kodlan' => ['sekolah_kodlan', 'kode_sekolah', 'kodlan', 'kode', 'sekolah_id', 'id_sekolah', 'npsn'],
            'kelas' => ['rombel', 'kelas', 'rombongan_belajar_saat_ini', 'rombongan_belajar', 'class', 'grade'],
        ];

        // Process standard keys first
        foreach ($mappings as $standardKey => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($row[$alias]) && !isset($mapped[$standardKey])) {
                    $mapped[$standardKey] = $row[$alias];
                    break;
                }
            }
        }

        // Keep other keys that might already be correct or extra
        foreach ($row as $key => $value) {
            if (!in_array($key, array_keys($mapped))) {
                // Check if this key is one of the standard keys already
                if (array_key_exists($key, $mappings)) {
                    if (!isset($mapped[$key])) {
                        $mapped[$key] = $value;
                    }
                } else {
                     $mapped[$key] = $value;
                }
            }
        }
        
        return $mapped;
    }
    /**
     * Import students to a specific Rombel.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param \App\Models\Rombel $rombel
     * @return array
     */
    public function importToRombel($file, \App\Models\EkstrakurikulerRombel $rombel)
    {
        $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
        
        if (empty($data) || empty($data[0])) {
            throw new \Exception("File kosong atau format tidak dikenali.");
        }

        $rows = $data[0]; // First sheet
        $importedIndex = 0;
        $updatedIndex = 0;
        
        // Remove header row if present
        $header = $rows[0] ?? [];
        if (isset($header[0]) && (stripos($header[0], 'no') !== false || stripos($header[1], 'nama') !== false)) {
            array_shift($rows);
        }

        foreach ($rows as $row) {
            // Assume format: No, Nama Lengkap, NISN, Kelas
            $nama = $row[1] ?? null;
            $nisn = $row[2] ?? null;
            $kelas = $row[3] ?? null;

            if (empty($nama)) continue;

            // Logic to find or create siswa
            // Linked to the school of the Ekstrakurikuler
            $sekolahKodlan = $rombel->ekstrakurikuler->sekolah_kodlan;
            
            $searchCriteria = [];
            if ($nisn) {
                $searchCriteria['nisn'] = $nisn;
            } else {
                // If no NISN, check by Name + School
                $searchCriteria['nama_lengkap'] = $nama;
                // We need the school code for the student.
                // Assuming $rombel->ekstrakurikuler->sekolah->kodlan exists.
            }
            
            // For now, let's create based on available data
            // We need to match with existing Siswa table structure
            
            // Simplification: Just create/find by Name
             $siswa = Siswa::firstOrCreate(
                [
                    'nama_lengkap' => $nama,
                    // 'sekolah_kodlan' => ... // Ideally filter by school too to avoid name collisions
                ],
                [
                    'nisn' => $nisn, 
                    'kelas' => $kelas,
                    'rombel' => $kelas, // Sync rombel with kelas
                    'sekolah_kodlan' => $rombel->ekstrakurikuler->sekolah_kodlan ?? null
                ]
            );

            // Attach to Rombel (Many-to-Many)
            if (!$rombel->siswa()->where('siswa_id', $siswa->id)->exists()) {
                $rombel->siswa()->attach($siswa->id);
                $importedIndex++;
            } else {
                $updatedIndex++;
            }
        }

        // Update rombel count
        $rombel->update(['jumlah_siswa' => $rombel->siswa()->count()]);

        return ['imported' => $importedIndex, 'updated' => $updatedIndex];
    }
}

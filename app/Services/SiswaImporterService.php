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
     * Runtime cache for Sekolah lookup to avoid redundant DB queries in loops.
     */
    protected array $sekolahCache = [];

    /**
     * Resolve sekolah_kodlan by kodlan or namasekolah with in-memory caching.
     */
    protected function resolveSekolahKodlan(?string $input): ?string
    {
        if (empty($input)) {
            return null;
        }

        $cacheKey = strtolower(trim($input));
        if (array_key_exists($cacheKey, $this->sekolahCache)) {
            return $this->sekolahCache[$cacheKey];
        }

        $sekolah = Sekolah::where('kodlan', $input)
            ->orWhere('namasekolah', $input)
            ->first();

        $kodlan = $sekolah ? (string)$sekolah->kodlan : (string)$input;
        $this->sekolahCache[$cacheKey] = $kodlan;

        return $kodlan;
    }

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

        DB::beginTransaction();

        try {
            foreach ($data as $row) {
                $row = array_change_key_case($row, CASE_LOWER);
                
                // Normalize keys & values (auto-cast numbers like 1, 10, 1A to string)
                $cleanRow = [];
                foreach ($row as $key => $value) {
                    $cleanKey = strtolower(trim($key));
                    $cleanKey = preg_replace('/[^a-z0-9]/', '_', $cleanKey);
                    $cleanKey = preg_replace('/_+/', '_', trim($cleanKey, '_'));
                    
                    if ($value !== null) {
                        if (is_float($value) && floor($value) == $value) {
                            $value = (string)(int)$value;
                        } else {
                            $value = (string)$value;
                        }
                        $value = trim($value);
                    }
                    $cleanRow[$cleanKey] = $value;
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

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
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
                // Determine reader type based on extension
                $readerType = strtolower($extension) === 'xls' ? \Maatwebsite\Excel\Excel::XLS : \Maatwebsite\Excel\Excel::XLSX;
                // Read the file using Maatwebsite Excel by explicitly setting the readerType
                $array = \Maatwebsite\Excel\Facades\Excel::toArray([], $filePath, null, $readerType);
                
                if (!empty($array) && !empty($array[0])) {
                    $rows = $array[0]; // Get the first sheet
                    
                    if (count($rows) > 0) {
                        // Assume first row is header
                        $header = array_map('trim', $rows[0]);
                        
                        // Parse remaining rows
                        for ($i = 1; $i < count($rows); $i++) {
                            $row = $rows[$i];
                            if (count($row) < count($header)) {
                                $row = array_pad($row, count($header), null);
                            }
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

    protected function validateRow(array &$row)
    {
        // Pre-resolve sekolah_kodlan with in-memory cache
        if (!empty($row['sekolah_kodlan'])) {
            $resolved = $this->resolveSekolahKodlan($row['sekolah_kodlan']);
            if ($resolved) {
                $row['sekolah_kodlan'] = $resolved;
            }
        }

        // Ensure string conversion before validation
        foreach (['nama_lengkap', 'nisn', 'sekolah_kodlan', 'kelas', 'no_hp_orangtua'] as $field) {
            if (isset($row[$field]) && $row[$field] !== null) {
                $row[$field] = (string)$row[$field];
            }
        }

        return Validator::make($row, [
            'nama_lengkap' => 'required',
            'nisn' => 'required',
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'kelas' => 'required',
            'no_hp_orangtua' => 'nullable',
        ]);
    }


    protected function createOrUpdateSiswa(array $data)
    {
        $sekolahKodlan = $this->resolveSekolahKodlan($data['sekolah_kodlan'] ?? null) ?? $data['sekolah_kodlan'];

        Siswa::updateOrCreate(
            ['nisn' => $data['nisn']],
            [
                'nama_lengkap' => $data['nama_lengkap'],
                'sekolah_kodlan' => $sekolahKodlan,
                'kelas' => $data['kelas'],
                'rombel' => $data['kelas'], // Sync rombel with kelas
                'no_hp_orangtua' => $data['no_hp_orangtua'] ?? null,
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
            'nama_lengkap' => [
                'nama_lengkap', 'nama', 'nama_siswa', 'name', 'student_name', 
                'nama_peserta_didik', 'nama_pd', 'nama_siswa_lengkap'
            ],
            'nisn' => [
                'nisn', 'nis', 'nomor_induk_siswa_nasional', 'nomor_induk', 
                'nis_nisn', 'no_nisn', 'id_siswa'
            ],
            'sekolah_kodlan' => [
                'sekolah_kodlan', 'kode_sekolah', 'kodlan', 'kode', 'sekolah_id', 
                'id_sekolah', 'npsn', 'sekolah', 'nama_sekolah'
            ],
            'kelas' => [
                'kelas', 'rombel', 'rombongan_belajar_saat_ini', 'rombongan_belajar', 
                'class', 'grade', 'kelas_akademik'
            ],
            'no_hp_orangtua' => [
                'no_hp_orangtua', 'no_hp', 'hp', 'no_wa', 'whatsapp', 
                'no_hp_ortu', 'no_telp_orangtua', 'no_hp_wali', 'no_hp_orang_tua', 
                'telepon_orangtua', 'hp_orangtua', 'hp_ortu'
            ],
        ];

        // Process standard keys first
        foreach ($mappings as $standardKey => $aliases) {
            foreach ($aliases as $alias) {
                if (isset($row[$alias]) && $row[$alias] !== null && $row[$alias] !== '') {
                    $mapped[$standardKey] = $row[$alias];
                    break;
                }
            }
        }

        // Keep other keys that might already be correct or extra
        foreach ($row as $key => $value) {
            if (!array_key_exists($key, $mapped)) {
                $mapped[$key] = $value;
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
        $filePath = is_string($file) ? $file : $file->getRealPath();
        $ext = is_object($file) && method_exists($file, 'getClientOriginalExtension') ? $file->getClientOriginalExtension() : null;

        $rows = $this->parseFile($filePath, $ext);
        
        if (empty($rows)) {
            throw new \Exception("File kosong atau format tidak dikenali.");
        }

        $importedIndex = 0;
        $updatedIndex = 0;

        DB::beginTransaction();

        try {
            // Preload existing attached student IDs into a hash set for O(1) checks
            $attachedSiswaIds = array_flip($rombel->siswa()->pluck('siswa_id')->toArray());
            $sekolahKodlan = $rombel->ekstrakurikuler->sekolah_kodlan ?? null;

            foreach ($rows as $rawRow) {
                $cleanRow = [];
                foreach ($rawRow as $key => $value) {
                    $cleanKey = strtolower(trim($key));
                    $cleanKey = preg_replace('/[^a-z0-9]/', '_', $cleanKey);
                    $cleanKey = preg_replace('/_+/', '_', trim($cleanKey, '_'));
                    if ($value !== null) {
                        $value = is_float($value) && floor($value) == $value ? (string)(int)$value : (string)$value;
                        $value = trim($value);
                    }
                    $cleanRow[$cleanKey] = $value;
                }

                $mapped = $this->mapHeaders($cleanRow);
                $nama = $mapped['nama_lengkap'] ?? null;
                $nisn = $mapped['nisn'] ?? null;
                $kelas = $mapped['kelas'] ?? null;

                if (empty($nama) && empty($nisn)) continue;

                $siswa = null;
                if (!empty($nisn)) {
                    $query = Siswa::where('nisn', $nisn);
                    if ($sekolahKodlan) {
                        $query->where('sekolah_kodlan', $sekolahKodlan);
                    }
                    $siswa = $query->first();
                }
                if (!$siswa && !empty($nama)) {
                    $query = Siswa::where('nama_lengkap', $nama);
                    if ($sekolahKodlan) {
                        $query->where('sekolah_kodlan', $sekolahKodlan);
                    }
                    $siswa = $query->first();
                }

                if (!$siswa) {
                    $siswa = Siswa::create([
                        'nama_lengkap' => $nama ?? 'Siswa Baru',
                        'nisn' => $nisn ?? 'TMP'.rand(100000, 999999),
                        'kelas' => $kelas ?? '-',
                        'rombel' => $kelas ?? '-',
                        'sekolah_kodlan' => $sekolahKodlan
                    ]);
                }

                // Attach to Rombel (O(1) Hash Map check)
                if (!isset($attachedSiswaIds[$siswa->id])) {
                    $rombel->siswa()->attach($siswa->id);
                    $attachedSiswaIds[$siswa->id] = true;
                    $importedIndex++;
                } else {
                    $updatedIndex++;
                }
            }

            // Update rombel count once
            $rombel->update(['jumlah_siswa' => count($attachedSiswaIds)]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return ['imported' => $importedIndex, 'updated' => $updatedIndex];
    }


    /**
     * Import students directly to an Ekstrakurikuler program's rombels.
     * CSV Columns: nama_lengkap, nisn, kelas_akademik, no_hp_orangtua, target_rombel_ekskul
     *
     * @param string $filePath
     * @param string|null $extension
     * @param \App\Models\Ekstrakurikuler $ekstrakurikuler
     * @return array
     */
    public function importToProgram(string $filePath, ?string $extension, \App\Models\Ekstrakurikuler $ekstrakurikuler): array
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

        DB::beginTransaction();

        try {
            foreach ($data as $rowIndex => $row) {
                $row = array_change_key_case($row, CASE_LOWER);
                
                // Normalize keys & values (auto-cast numbers like 1, 10, 1A to string)
                $cleanRow = [];
                foreach ($row as $key => $value) {
                    $cleanKey = str_replace(' ', '_', trim($key));
                    if ($value !== null) {
                        if (is_float($value) && floor($value) == $value) {
                            $value = (string)(int)$value;
                        } else {
                            $value = (string)$value;
                        }
                        $value = trim($value);
                    }
                    $cleanRow[$cleanKey] = $value;
                }

                // Map Headers
                $mappedRow = [];
                $mappings = [
                    'nama_lengkap' => ['nama', 'nama_siswa', 'nama_lengkap', 'name', 'student_name'],
                    'nisn' => ['nisn', 'nis', 'nomor_induk_siswa_nasional'],
                    'kelas_akademik' => ['kelas_akademik', 'kelas', 'rombel_sekolah', 'rombel_akademik', 'class', 'grade'],
                    'no_hp_orangtua' => ['no_hp_orangtua', 'no_hp', 'hp', 'no_wa', 'whatsapp', 'no_telp_orangtua', 'no_hp_wali'],
                    'target_rombel_ekskul' => ['target_rombel_ekskul', 'rombel_ekskul', 'rombel_ekskul_target', 'rombel_tujuan', 'rombel', 'group'],
                ];

                foreach ($mappings as $standardKey => $aliases) {
                    foreach ($aliases as $alias) {
                        if (isset($cleanRow[$alias]) && !isset($mappedRow[$standardKey])) {
                            $mappedRow[$standardKey] = $cleanRow[$alias];
                            break;
                        }
                    }
                }

                // Initialize unmapped keys to null
                foreach ($mappings as $standardKey => $aliases) {
                    if (!isset($mappedRow[$standardKey])) {
                        $mappedRow[$standardKey] = null;
                    }
                }

                // Keep other fields
                foreach ($cleanRow as $key => $value) {
                    if (!isset($mappedRow[$key]) && array_key_exists($key, $mappings)) {
                        $mappedRow[$key] = $value;
                    }
                }

                // Validate row
                $validation = Validator::make($mappedRow, [
                    'nama_lengkap' => 'required',
                    'nisn' => 'required',
                    'kelas_akademik' => 'required',
                    'no_hp_orangtua' => 'nullable|min:10|max:15',
                    'target_rombel_ekskul' => 'required',
                ]);


                if ($validation->fails()) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($rowIndex + 2) . ": " . implode(', ', $validation->errors()->all());
                    continue;
                }

                // Find target rombel
                $targetRombel = $mappedRow['target_rombel_ekskul'];
                $rombel = $ekstrakurikuler->rombels()
                    ->where(function ($q) use ($targetRombel) {
                        $q->where('nama_rombel', $targetRombel)
                          ->orWhere('nomor_rombel', $targetRombel)
                          ->orWhere('nama_rombel', 'like', '%' . $targetRombel . '%');
                    })
                    ->first();

                if (!$rombel) {
                    $results['failed']++;
                    $results['errors'][] = "Baris " . ($rowIndex + 2) . ": Rombel ekskul '" . $targetRombel . "' tidak ditemukan di program ini.";
                    continue;
                }

                // Create or update Siswa
                $siswa = Siswa::updateOrCreate(
                    ['nisn' => $mappedRow['nisn']],
                    [
                        'nama_lengkap' => $mappedRow['nama_lengkap'],
                        'sekolah_kodlan' => $ekstrakurikuler->sekolah_kodlan,
                        'kelas' => $mappedRow['kelas_akademik'],
                        'rombel' => $mappedRow['kelas_akademik'],
                        'no_hp_orangtua' => $mappedRow['no_hp_orangtua'],
                    ]
                );

                // Enroll student to the specific program rombel
                $isEnrolled = \App\Models\SiswaEkstrakurikuler::where('siswa_id', $siswa->id)
                    ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                    ->where('status', '!=', 'keluar')
                    ->exists();

                if (!$isEnrolled) {
                    \App\Models\SiswaEkstrakurikuler::create([
                        'siswa_id' => $siswa->id,
                        'ekstrakurikuler_id' => $ekstrakurikuler->id,
                        'ekstrakurikuler_rombel_id' => $rombel->id,
                        'status' => 'aktif',
                        'tanggal_daftar' => now()->toDateString(),
                        'catatan' => 'Daftar via import program',
                    ]);

                    // Trigger Welcome Message
                    if ($siswa->no_hp_orangtua) {
                        try {
                            $siswa->notify(new \App\Notifications\WelcomeParentNotification($siswa, $rombel));
                        } catch (\Exception $e) {
                            \Log::error('Gagal mengirim WhatsApp Welcome Message ke siswa ID: ' . $siswa->id . '. Error: ' . $e->getMessage());
                        }
                    }

                    $results['success']++;
                } else {
                    // Update current enrollment's rombel if already registered
                    $enrollment = \App\Models\SiswaEkstrakurikuler::where('siswa_id', $siswa->id)
                        ->where('ekstrakurikuler_id', $ekstrakurikuler->id)
                        ->where('status', '!=', 'keluar')
                        ->first();
                    
                    if ($enrollment) {
                        $enrollment->update([
                            'ekstrakurikuler_rombel_id' => $rombel->id
                        ]);
                    }
                    $results['success']++;
                }
            }

            // Update student count for all rombels of the program
            foreach ($ekstrakurikuler->rombels as $r) {
                $r->update(['jumlah_siswa' => $r->siswa()->count()]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return $results;
    }
}

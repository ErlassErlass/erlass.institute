<?php

namespace App\Services\Ekstrakurikuler;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EkstrakurikulerFormService
{
    protected $schedulingService;

    public function __construct(\App\Services\SchedulingService $schedulingService)
    {
        $this->schedulingService = $schedulingService;
    }

    /**
     * Preview sessions for rombels
     */
    public function previewSessions(): array
    {
        $formData = $this->getFormData();
        
        if (empty($formData['rombels'])) {
            return [
                'success' => false,
                'message' => 'Data rombel belum tersedia',
            ];
        }

        $previews = [];

        try {
            foreach ($formData['rombels'] as $index => $rombelData) {
                if (!isset($rombelData['tanggal_mulai']) || !isset($rombelData['hari'])) {
                    continue;
                }

                // Create temporary rombel object untuk preview
                $tempRombel = new \App\Models\EkstrakurikulerRombel([
                    'nama_rombel' => "Rombel {$index}",
                    'nomor_rombel' => $index,
                    'tanggal_mulai' => $rombelData['tanggal_mulai'],
                    'tanggal_selesai' => $rombelData['tanggal_selesai'],
                    'hari' => $rombelData['hari'],
                    'jam_mulai' => $rombelData['jam_mulai'],
                    'jam_selesai' => \Carbon\Carbon::createFromFormat('H:i', $rombelData['jam_mulai'])->addHours(2)->format('H:i'),
                    'total_pertemuan' => $rombelData['total_pertemuan'],
                    'frekuensi' => \App\Models\EkstrakurikulerRombel::FREKUENSI_MINGGUAN,
                ]);

                // Calculate session dates using SchedulingService
                $sessionDates = $this->schedulingService->calculateSessionDates($tempRombel, [
                    'skip_holidays' => true,
                ]);

                $previews[] = [
                    'rombel_info' => [
                        'nama' => $tempRombel->nama_rombel,
                        'nomor' => $tempRombel->nomor_rombel,
                        'hari' => ucfirst($rombelData['hari']),
                        'waktu' => $rombelData['jam_mulai'].' - '.$tempRombel->jam_selesai,
                        'periode' => \Carbon\Carbon::parse($rombelData['tanggal_mulai'])->format('d/m/Y').' - '.
                                   \Carbon\Carbon::parse($rombelData['tanggal_selesai'])->format('d/m/Y'),
                        'total_pertemuan_target' => $rombelData['total_pertemuan'],
                        'jumlah_siswa' => $rombelData['jumlah_siswa'],
                        'ruangan' => $rombelData['ruangan'] ?? "Ruang {$index}",
                    ],
                    'sessions_preview' => $sessionDates->take(5)->map(function ($date, $sessionIndex) {
                        return [
                            'nomor_pertemuan' => $sessionIndex + 1,
                            'tanggal' => $date->format('d/m/Y'),
                            'hari' => $date->locale('id')->translatedFormat('l'),
                            'bulan_tahun' => $date->format('M Y'),
                        ];
                    })->values(),
                    'total_sessions_generated' => $sessionDates->count(),
                    'sessions_summary' => [
                        'first_session' => $sessionDates->first()?->format('d/m/Y'),
                        'last_session' => $sessionDates->last()?->format('d/m/Y'),
                        'total_weeks' => $sessionDates->count(),
                    ],
                ];
            }

            return [
                'success' => true,
                'previews' => $previews,
                'summary' => [
                    'total_rombels' => count($previews),
                    'total_sessions' => array_sum(array_column($previews, 'total_sessions_generated')),
                    'earliest_start' => collect($previews)->min('sessions_summary.first_session'),
                    'latest_end' => collect($previews)->max('sessions_summary.last_session'),
                ],
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error previewing sessions: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal generate preview: '.$e->getMessage(),
            ];
        }
    }



    /**
     * Initialize form data
     */
    public function initializeForm()
    {
        Session::forget('ekstrakurikuler_form_data');
        return $this->getFormData();
    }

    /**
     * Get form data from session with defaults
     */
    public function getFormData()
    {
        return Session::get('ekstrakurikuler_form_data', [
            'total_rombel' => 2,
            'total_siswa' => 0,
            'total_ruangan' => 1,
            'status' => 'draft',
        ]);
    }

    /**
     * Store form data for specific step
     */
    public function storeStepData(Request $request, int $step)
    {
        $formData = $this->getFormData();
        $stepData = $this->extractStepData($request, $step);
        $formData = array_merge($formData, $stepData);
        
        Session::put('ekstrakurikuler_form_data', $formData);
        
        return $formData;
    }

    /**
     * Extract data for specific step
     */
    protected function extractStepData(Request $request, int $step): array
    {
        return match($step) {
            1 => $this->extractStep1Data($request),
            2 => $this->extractStep2Data($request),
            3 => $this->extractStep3Data($request),
            4 => $this->extractStep4Data($request),
            5, 6, 7, 8, 9 => $this->extractRombelData($request, $step),
            default => [],
        };
    }

    /**
     * Extract step 1 data (Basic Program Info)
     */
    protected function extractStep1Data(Request $request): array
    {
        return $request->only([
            'kategori_program', 'user_id_sales', 'region', 'city', 
            'jenis_pembayaran', 'jenis_alat', 'jumlah_siswa_per_alat', 'deskripsi',
        ]);
    }

    /**
     * Extract step 2 data (School Selection & Details)
     */
    protected function extractStep2Data(Request $request): array
    {
        return $request->only([
            'sekolah_kodlan', 'alamat_lengkap', 'google_maps_link',
            'jarak_km', 'kepala_sekolah', 'penanggung_jawab', 'no_telepon',
        ]);
    }

    /**
     * Extract step 3 data (Technical Requirements)
     */
    protected function extractStep3Data(Request $request): array
    {
        return $request->only([
            'koneksi_internet', 'proyektor',
            'keterangan_proyektor', 'kabel_hdmi', 'kabel_vga', 'kabel_roll', 'keterangan_kabel',
        ]);
    }

    /**
     * Extract step 4 data (Class Structure)
     */
    protected function extractStep4Data(Request $request): array
    {
        return $request->only([
            'total_siswa', 'total_ruangan', 'total_rombel',
        ]);
    }

    /**
     * Extract rombel data for steps 5-9
     */
    protected function extractRombelData(Request $request, int $step): array
    {
        $rombelNumber = $step - 4;
        $prefix = "rombel_{$rombelNumber}_";
        
        $jamMulai = $request->input($prefix . 'jam_mulai');
        $jamSelesai = null;
        
        if ($jamMulai) {
            $jamSelesai = \Carbon\Carbon::parse($jamMulai)->addHours(2)->format('H:i');
        }

        return [
            'rombels' => [
                $rombelNumber => [
                    'total_pertemuan' => $request->input($prefix . 'total_pertemuan'),
                    'tanggal_mulai' => $request->input($prefix . 'tanggal_mulai'),
                    'tanggal_selesai' => $request->input($prefix . 'tanggal_selesai'),
                    'hari' => $request->input($prefix . 'hari'),
                    'jam_mulai' => $jamMulai,
                    'jam_selesai' => $jamSelesai, // Auto calculated
                    'jumlah_siswa' => $request->input($prefix . 'jumlah_siswa'),
                    'ruangan' => $request->input($prefix . 'ruangan', ''),
                    'keterangan_ruangan' => $request->input($prefix . 'keterangan_ruangan', ''),
                ],
            ],
        ];
    }

    /**
     * Get validation rules for specific step
     */
    public function getStepValidationRules(int $step): array
    {
        return match($step) {
            1 => $this->getStep1ValidationRules(),
            2 => $this->getStep2ValidationRules(),
            3 => $this->getStep3ValidationRules(),
            4 => $this->getStep4ValidationRules(),
            5, 6, 7, 8, 9 => $this->getRombelValidationRules($step),
            default => [],
        };
    }

    /**
     * Step 1 validation rules
     */
    protected function getStep1ValidationRules(): array
    {
        return [
            'kategori_program' => 'required|string|in:' . implode(',', [
                \App\Models\Ekstrakurikuler::KATEGORI_CODING_SCRATCH,
                \App\Models\Ekstrakurikuler::KATEGORI_ENGLISH_COURSE,
                \App\Models\Ekstrakurikuler::KATEGORI_MICROBIT_LEARNING,
                \App\Models\Ekstrakurikuler::KATEGORI_PICTOBLOX_AI,
                \App\Models\Ekstrakurikuler::KATEGORI_ROBOTIK_EXPLORER,
                \App\Models\Ekstrakurikuler::KATEGORI_ROBOTIK_JIMU,
            ]),
            'user_id_sales' => 'required|exists:users,id',
            'region' => 'nullable|string',
            'city' => 'required|string',
            'jenis_pembayaran' => 'required|string',
            'jenis_alat' => 'nullable|string',
            'jumlah_siswa_per_alat' => 'nullable|integer',
        ];
    }

    /**
     * Step 2 validation rules
     */
    protected function getStep2ValidationRules(): array
    {
        return [
            'sekolah_kodlan' => 'required|exists:sekolah,kodlan',
            'alamat_lengkap' => 'required|string',
            'google_maps_link' => 'required|url',
            'jarak_km' => 'required|numeric|min:0',
            'kepala_sekolah' => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'no_telepon' => 'required|string|max:20',
        ];
    }

    /**
     * Step 3 validation rules
     */
    protected function getStep3ValidationRules(): array
    {
        return [
            'koneksi_internet' => 'required|in:ada,tidak_ada,tidak_diketahui',
            'proyektor' => 'required|in:ada,tidak_ada,tidak_diketahui',
            'keterangan_proyektor' => 'nullable|string',
            'kabel_hdmi' => 'required|in:ada,tidak_ada,tidak_diketahui',
            'kabel_vga' => 'required|in:ada,tidak_ada,tidak_diketahui',
            'kabel_roll' => 'required|in:ada,tidak_ada,tidak_diketahui',
            'keterangan_kabel' => 'nullable|string',
        ];
    }

    /**
     * Step 4 validation rules
     */
    protected function getStep4ValidationRules(): array
    {
        return [
            'total_siswa' => 'required|integer|min:1',
            'total_ruangan' => 'required|integer|min:1',
            'total_rombel' => 'required|integer|min:1|max:5',
        ];
    }

    /**
     * Rombel validation rules for steps 5-9
     */
    protected function getRombelValidationRules(int $step): array
    {
        $rombelNumber = $step - 4;
        return [
            "rombel_{$rombelNumber}_total_pertemuan" => 'required|integer|min:1',
            "rombel_{$rombelNumber}_tanggal_mulai" => 'required|date',
            "rombel_{$rombelNumber}_tanggal_selesai" => 'required|date|after_or_equal:rombel_' . $rombelNumber . '_tanggal_mulai',
            "rombel_{$rombelNumber}_hari" => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            "rombel_{$rombelNumber}_jam_mulai" => 'required|date_format:H:i',
            "rombel_{$rombelNumber}_jumlah_siswa" => 'required|integer|min:1',
        ];
    }

    /**
     * Validate final form data
     */
    public function validateFinalForm(array $formData): void
    {
        if (empty($formData['kategori_program'])) {
            throw new \Exception('Nama program harus diisi');
        }

        if (empty($formData['total_rombel']) || $formData['total_rombel'] < 1) {
            throw new \Exception('Jumlah rombel harus minimal 1');
        }

        if (empty($formData['rombels']) || count($formData['rombels']) < $formData['total_rombel']) {
            throw new \Exception('Data rombel tidak lengkap');
        }

        // Check student count consistency
        $totalSiswaTarget = $formData['total_siswa'] ?? 0;
        $totalSiswaActual = 0;
        foreach ($formData['rombels'] as $rombelNumber => $rombel) {
            if ($rombelNumber <= $formData['total_rombel']) {
                $totalSiswaActual += $rombel['jumlah_siswa'] ?? 0;
            }
        }

        if ($totalSiswaActual != $totalSiswaTarget) {
            throw new \Exception("Total siswa di rombel ({$totalSiswaActual}) tidak sesuai dengan target ({$totalSiswaTarget})");
        }
    }

    /**
     * Clear form data
     */
    public function clearFormData()
    {
        Session::forget('ekstrakurikuler_form_data');
    }

    /**
     * Check if step is valid
     */
    public function isValidStep(int $step): bool
    {
        return $step >= 1 && $step <= 10;
    }

    /**
     * Check if step is final
     */
    public function isFinalStep(int $step): bool
    {
        return $step === 10;
    }

    /**
     * Get next step number based on total_rombel
     */
    public function calculateNextStep(int $currentStep, array $formData): int
    {
        $totalRombel = $formData['total_rombel'] ?? 2;
        
        switch ($currentStep) {
            case 4:
                return 5;
            case 5:
            case 6:
            case 7:
            case 8:
            case 9:
                $rombelNumber = $currentStep - 4;
                if ($rombelNumber >= $totalRombel) {
                    return 10;
                }
                return $currentStep + 1;
            case 10:
                return 10;
            default:
                return $currentStep + 1;
        }
    }

    /**
     * Get previous step number based on total_rombel
     */
    public function calculatePreviousStep(int $currentStep, array $formData): int
    {
        $totalRombel = $formData['total_rombel'] ?? 2;
        
        switch ($currentStep) {
            case 5:
                return 4;
            case 10:
                return 4 + $totalRombel;
            default:
                return max(1, $currentStep - 1);
        }
    }

    /**
     * Validate specific step
     */
    public function validateStep(\Illuminate\Http\Request $request, int $step): void
    {
        $rules = $this->getStepValidationRules($step);
        $request->validate($rules);
    }

    /**
     * Get step data from request
     */
    public function getStepData(\Illuminate\Http\Request $request, int $step): array
    {
        return $this->extractStepData($request, $step);
    }

    /**
     * Save step data to session
     */
    public function saveStepData(array $stepData): void
    {
        $formData = $this->getFormData();
        
        // Use recursive replacement to preserve rombels array structure
        $formData = array_replace_recursive($formData, $stepData);
        
        \Illuminate\Support\Facades\Session::put('ekstrakurikuler_form_data', $formData);
    }



    /**
     * Store ekstrakurikuler with complete data
     */
    public function storeEkstrakurikuler(\Illuminate\Http\Request $request): \App\Models\Ekstrakurikuler
    {
        $formData = $this->getFormData();
        \Illuminate\Support\Facades\Log::info('Attempting to store Ekstrakurikuler', ['form_data_keys' => array_keys($formData)]);
        
        // Validate final form
        try {
            $this->validateFinalForm($formData);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Validation failed in storeEkstrakurikuler', [
                'error' => $e->getMessage(),
                'form_data' => $formData
            ]);
            throw $e;
        }

        \Illuminate\Support\Facades\Log::info('Validation passed, proceeding to database transaction');

        // Calculate totals and dates from rombels
        $totalSiswaRombel = 0;
        $tanggalMulaiEarliest = null;
        $tanggalSelesaiLatest = null;
        $totalPertemuanAll = 0;

        if (isset($formData['rombels'])) {
            $totalRombel = $formData['total_rombel'] ?? 1;
            for ($i = 1; $i <= $totalRombel; $i++) {
                if (isset($formData['rombels'][$i])) {
                    $rombel = $formData['rombels'][$i];
                    $totalSiswaRombel += $rombel['jumlah_siswa'];
                    $totalPertemuanAll += $rombel['total_pertemuan'];

                    $mulai = \Carbon\Carbon::parse($rombel['tanggal_mulai']);
                    $selesai = \Carbon\Carbon::parse($rombel['tanggal_selesai']);

                    if (! $tanggalMulaiEarliest || $mulai->lt($tanggalMulaiEarliest)) {
                        $tanggalMulaiEarliest = $mulai;
                    }

                    if (! $tanggalSelesaiLatest || $selesai->gt($tanggalSelesaiLatest)) {
                        $tanggalSelesaiLatest = $selesai;
                    }
                }
            }
        }
        

        return \Illuminate\Support\Facades\DB::transaction(function () use ($formData, $tanggalMulaiEarliest, $tanggalSelesaiLatest, $totalPertemuanAll, $totalSiswaRombel) {
            // Create ekstrakurikuler
            $ekstrakurikuler = \App\Models\Ekstrakurikuler::create([
                'kategori_program' => $formData['kategori_program'],
                'user_id_sales' => $formData['user_id_sales'],
                'region' => $formData['region'] ?? null,
                'status' => \App\Models\Ekstrakurikuler::STATUS_AKTIF,
                'tanggal_aktivasi' => now(),
                'diaktifkan_oleh' => auth()->id(),
                'deskripsi' => $formData['deskripsi'] ?? null,
                'sekolah_kodlan' => $formData['sekolah_kodlan'],
                'alamat_lengkap' => $formData['alamat_lengkap'],
                'google_maps_link' => $formData['google_maps_link'] ?? null,
                'jarak_km' => $formData['jarak_km'],
                'kepala_sekolah' => $formData['kepala_sekolah'],
                'penanggung_jawab' => $formData['penanggung_jawab'],
                'no_telepon' => $formData['no_telepon'],
                'koneksi_internet' => $formData['koneksi_internet'],
                'proyektor' => $formData['proyektor'],
                'keterangan_proyektor' => $formData['keterangan_proyektor'] ?? null,
                'kabel_hdmi' => $formData['kabel_hdmi'],
                'kabel_vga' => $formData['kabel_vga'],
                'kabel_roll' => $formData['kabel_roll'],
                'keterangan_kabel' => $formData['keterangan_kabel'] ?? null,
                'total_siswa' => $totalSiswaRombel, // Use calculated total
                'total_ruangan' => $formData['total_ruangan'],
                'total_rombel' => $formData['total_rombel'],
                'tanggal_mulai' => $tanggalMulaiEarliest,
                'tanggal_selesai' => $tanggalSelesaiLatest,
                'total_pertemuan' => $totalPertemuanAll,
                'frekuensi' => \App\Models\Ekstrakurikuler::FREKUENSI_MINGGUAN,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);

            // Create rombels and generate sessions
            if (isset($formData['rombels'])) {
                $totalRombelLimit = $formData['total_rombel'] ?? 1;
                for ($rombelNumber = 1; $rombelNumber <= $totalRombelLimit; $rombelNumber++) {
                    if (isset($formData['rombels'][$rombelNumber])) {
                        $rombelData = $formData['rombels'][$rombelNumber];
                        $rombel = \App\Models\EkstrakurikulerRombel::create([
                            'ekstrakurikuler_id' => $ekstrakurikuler->id,
                            'nama_rombel' => "Rombel {$rombelNumber}",
                            'nomor_rombel' => $rombelNumber,
                            'total_pertemuan' => $rombelData['total_pertemuan'],
                            'tanggal_mulai' => $rombelData['tanggal_mulai'],
                            'tanggal_selesai' => $rombelData['tanggal_selesai'],
                            'hari' => $rombelData['hari'],
                            'jam_mulai' => $rombelData['jam_mulai'],
                            'jam_selesai' => $rombelData['jam_selesai'] ?? \Carbon\Carbon::parse($rombelData['jam_mulai'])->addHours(2)->format('H:i'), // Ensure fallback
                            'jumlah_siswa' => $rombelData['jumlah_siswa'],
                            'ruangan' => $rombelData['ruangan'] ?? '',
                            'keterangan_ruangan' => $rombelData['keterangan_ruangan'] ?? '',
                            'status' => \App\Models\EkstrakurikulerRombel::STATUS_BERLANGSUNG,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);

                        // AUTO GENERATE SCHEDULE
                        try {
                            $this->schedulingService->generateSessionsForRombel($rombel, ['replace_existing' => true]);
                        } catch (\Exception $e) {
                            \Illuminate\Support\Facades\Log::warning("Gagal generate session untuk rombel {$rombel->id}: " . $e->getMessage());
                        }
                    }
                }
            }

            // Clear form data after successful store
            $this->clearFormData();

            return $ekstrakurikuler;
        });
    }

    /**
     * Update existing ekstrakurikuler with rombel data
     */
    public function updateEkstrakurikuler(\App\Models\Ekstrakurikuler $ekstrakurikuler, array $validatedData): \App\Models\Ekstrakurikuler
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($ekstrakurikuler, $validatedData) {
            // Separate rombel data
            $rombelData = $validatedData['rombel'] ?? [];
            unset($validatedData['rombel']);
            unset($validatedData['city']); // City is not in DB table, strictly for UI/Form

            // Update main record
            $ekstrakurikuler->update($validatedData);

            // Update rombels if present
            if (!empty($rombelData)) {
                $totalSiswa = 0;
                $totalPertemuan = 0;
                $tanggalMulaiEarliest = null;
                $tanggalSelesaiLatest = null;

                foreach ($rombelData as $rombelId => $data) {
                    $rombel = $ekstrakurikuler->rombels()->find($rombelId);
                    if ($rombel) {
                        $rombel->update([
                            'jumlah_siswa' => $data['jumlah_siswa'],
                            'total_pertemuan' => $data['total_pertemuan'],
                            'ruangan' => $data['ruangan'],
                            'keterangan_ruangan' => $data['keterangan_ruangan'],
                            'hari' => $data['hari'],
                            'jam_mulai' => $data['jam_mulai'],
                            'tanggal_mulai' => $data['tanggal_mulai'],
                            'tanggal_selesai' => $data['tanggal_selesai'],
                            'updated_by' => auth()->id(),
                        ]);
                        
                        // Recalculate totals
                        $totalSiswa += $data['jumlah_siswa'];
                        $totalPertemuan += $data['total_pertemuan'];
                        
                        $mulai = \Carbon\Carbon::parse($data['tanggal_mulai']);
                        $selesai = \Carbon\Carbon::parse($data['tanggal_selesai']);

                        if (! $tanggalMulaiEarliest || $mulai->lt($tanggalMulaiEarliest)) {
                            $tanggalMulaiEarliest = $mulai;
                        }

                        if (! $tanggalSelesaiLatest || $selesai->gt($tanggalSelesaiLatest)) {
                            $tanggalSelesaiLatest = $selesai;
                        }
                    }
                }

                // Update totals in parent
                $ekstrakurikuler->update([
                    'total_siswa' => $totalSiswa,
                    'total_pertemuan' => $totalPertemuan,
                    'tanggal_mulai' => $tanggalMulaiEarliest ?? $ekstrakurikuler->tanggal_mulai,
                    'tanggal_selesai' => $tanggalSelesaiLatest ?? $ekstrakurikuler->tanggal_selesai,
                ]);
            }

            return $ekstrakurikuler;
        });
    }
}
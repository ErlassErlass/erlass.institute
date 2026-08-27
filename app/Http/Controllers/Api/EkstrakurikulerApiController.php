<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ekstrakurikuler\GetSekolahByCityRequest;
use App\Services\Ekstrakurikuler\EkstrakurikulerFormService;
use App\Services\Ekstrakurikuler\EkstrakurikulerQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * API Controller untuk endpoints ekstrakurikuler
 * Menangani semua API calls terkait ekstrakurikuler seperti:
 * - Form data management
 * - School selection
 * - Session preview
 */
class EkstrakurikulerApiController extends Controller
{
    protected EkstrakurikulerFormService $formService;
    protected EkstrakurikulerQueryService $queryService;

    public function __construct(
        EkstrakurikulerFormService $formService,
        EkstrakurikulerQueryService $queryService
    ) {
        $this->formService = $formService;
        $this->queryService = $queryService;
    }

    /**
     * Dapatkan form data sebagai JSON untuk AJAX requests
     */
    public function getFormData(): JsonResponse
    {
        try {
            $formData = $this->formService->getFormData();

            return response()->json([
                'success' => true,
                'data' => $formData,
                'message' => 'Form data berhasil diambil',
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting form data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil form data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Clear form session data
     */
    public function clearFormData(): JsonResponse
    {
        try {
            $this->formService->clearFormData();

            return response()->json([
                'success' => true,
                'message' => 'Form data berhasil dihapus',
            ]);

        } catch (\Exception $e) {
            Log::error('Error clearing form data', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus form data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API endpoint untuk mendapatkan sekolah berdasarkan kota
     */
    public function getSekolahByCity(GetSekolahByCityRequest $request): JsonResponse
    {
        try {
            $kota = $request->validated()['kota'];
            $sekolahList = $this->queryService->getSchoolsByCity($kota);

            return response()->json([
                'status' => 'success',
                'message' => 'Data sekolah berhasil diambil',
                'data' => $sekolahList->map(function ($sekolah) {
                    return [
                        'kodlan' => $sekolah->kodlan,
                        'namasekolah' => $sekolah->namasekolah,
                        'kotkab' => $sekolah->kotkab,
                        'kec' => $sekolah->kec,
                        'display_name' => $sekolah->namasekolah . ' - ' . $sekolah->kec . ', ' . $sekolah->kotkab,
                    ];
                }),
                'count' => $sekolahList->count(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting sekolah by city', [
                'kota' => $request->get('kota'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data sekolah',
                'error' => $e->getMessage(),
                'data' => [],
            ], 500);
        }
    }

    /**
     * Preview sessions yang akan di-generate berdasarkan data rombel
     */
    public function previewSessions(Request $request): JsonResponse
    {
        try {
            $preview = $this->formService->previewSessions();
            Log::info('previewSessions called', [
                'preview_summary' => $preview['summary'] ?? null,
                'rombel_count' => count($preview['previews'] ?? [])
            ]);

            return response()->json($preview);

        } catch (\Exception $e) {
            Log::error('Error previewing sessions', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal generate preview sessions: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Validasi data step untuk AJAX form validation
     */
    public function validateStep(Request $request): JsonResponse
    {
        try {
            $step = (int) $request->input('step', 1);
            
            // Validasi menggunakan form service
            $this->formService->validateStep($request, $step);

            return response()->json([
                'success' => true,
                'message' => 'Validasi berhasil',
                'step' => $step,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
                'step' => $request->input('step', 1),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error validating step', [
                'step' => $request->input('step'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat validasi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dropdown data untuk form (untuk AJAX loading)
     */
    public function getDropdownData(Request $request): JsonResponse
    {
        try {
            $type = $request->query('type');
            
            switch ($type) {
                case 'sekolah':
                    $data = $this->queryService->getFormCreationData();
                    $result = [
                        'sekolahs' => $data['sekolahs']->map(function ($sekolah) {
                            return [
                                'kodlan' => $sekolah->kodlan,
                                'text' => $sekolah->namasekolah . ' - ' . $sekolah->kec . ', ' . $sekolah->kotkab,
                                'kota' => $sekolah->kota,
                            ];
                        }),
                    ];
                    break;

                case 'sales':
                    $data = $this->queryService->getFormCreationData();
                    $result = [
                        'sales_users' => $data['salesUsers']->map(function ($user) {
                            return [
                                'id' => $user->id,
                                'text' => $user->nama_lengkap . ' (' . ucfirst($user->role) . ')',
                                'role' => $user->role,
                            ];
                        }),
                    ];
                    break;

                case 'city':
                    $data = $this->queryService->getFormCreationData();
                    $result = [
                        'cities' => collect($data['kotaOptions'])->map(function ($kota) {
                            return [
                                'value' => $kota,
                                'text' => $kota,
                            ];
                        })->values(),
                    ];
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid dropdown type');
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'message' => 'Dropdown data berhasil diambil',
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting dropdown data', [
                'type' => $request->query('type'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil dropdown data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Save step data via AJAX (untuk auto-save functionality)
     */
    public function saveStepData(Request $request): JsonResponse
    {
        try {
            $step = (int) $request->input('step', 1);
            
            // Validasi step
            $this->formService->validateStep($request, $step);
            
            // Get dan save step data
            $stepData = $this->formService->getStepData($request, $step);
            $this->formService->saveStepData($stepData);

            return response()->json([
                'success' => true,
                'message' => 'Data step berhasil disimpan',
                'step' => $step,
                'data_saved' => array_keys($stepData),
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error saving step data', [
                'step' => $request->input('step'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan data step',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get form progress/status
     */
    public function getFormProgress(): JsonResponse
    {
        try {
            $formData = $this->formService->getFormData();
            
            $progress = [
                'step1_completed' => isset($formData['kategori_program']) && isset($formData['user_id_sales']),
                'step2_completed' => isset($formData['sekolah_kodlan']) && isset($formData['alamat_lengkap']),
                'step3_completed' => isset($formData['koneksi_internet']) && isset($formData['proyektor']),
                'step4_completed' => isset($formData['total_rombel']) && $formData['total_rombel'] >= 1,
                'rombel_data' => [],
            ];

            // Check rombel completion
            if (isset($formData['total_rombel'])) {
                $totalRombel = (int) $formData['total_rombel'];
                for ($i = 1; $i <= $totalRombel; $i++) {
                    $progress['rombel_data'][$i] = isset($formData['rombels'][$i]) && 
                        isset($formData['rombels'][$i]['total_pertemuan']) &&
                        isset($formData['rombels'][$i]['tanggal_mulai']);
                }
            }

            $completedSteps = array_sum([
                $progress['step1_completed'] ? 1 : 0,
                $progress['step2_completed'] ? 1 : 0,
                $progress['step3_completed'] ? 1 : 0,
                $progress['step4_completed'] ? 1 : 0,
                array_sum($progress['rombel_data']),
            ]);

            $totalSteps = 4 + ($formData['total_rombel'] ?? 0);
            $progressPercentage = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'progress' => $progress,
                'completed_steps' => $completedSteps,
                'total_steps' => $totalSteps,
                'progress_percentage' => $progressPercentage,
                'can_submit' => $progressPercentage >= 100,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting form progress', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil progress form',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Search student by name specifically for "Tambah Siswa" feature
     */
    public function searchStudent(Request $request): JsonResponse
    {
        try {
            $search = trim($request->query('q'));
            
            if (empty($search) || strlen($search) < 3) {
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'message' => 'Query too short'
                ]);
            }

            $students = \App\Models\Siswa::with('sekolah:kodlan,namasekolah')
                ->where('nama_lengkap', 'like', "%{$search}%")
                ->select('id', 'nama_lengkap', 'sekolah_kodlan', 'rombel')
                ->limit(10)
                ->get()
                ->map(function($student) {
                    return [
                        'id' => $student->id,
                        'nama_lengkap' => $student->nama_lengkap,
                        'sekolah_nama' => $student->sekolah ? $student->sekolah->namasekolah : $student->sekolah_kodlan,
                        'rombel' => $student->rombel,
                    ];
                });
                
            return response()->json([
                'success' => true,
                'data' => $students
            ]);

        } catch (\Exception $e) {
            Log::error('Error searching student', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Store new student (Quick Add)
     */
    public function storeQuickStudent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nama_lengkap' => 'required|string|min:3|max:255',
                'sekolah_kodlan' => 'required|string|exists:sekolah,kodlan',
                'jenis_kelamin' => ['required', 'string', \Illuminate\Validation\Rule::in(['L', 'P'])],
                'kelas' => 'required|string|max:50',
                'no_hp_orangtua' => ['nullable', 'string', 'max:25'],
            ], [
                'jenis_kelamin.in' => 'Pilihan jenis kelamin tidak valid.',
            ]);

            $kelasValue = trim(strip_tags($request->kelas));
            $student = \App\Models\Siswa::create([
                'nama_lengkap' => trim(strip_tags($request->nama_lengkap)),
                'sekolah_kodlan' => $request->sekolah_kodlan,
                'jenis_kelamin' => $request->jenis_kelamin,
                // Generate temporary NISN: TEMP + UNIX Seconds + Random 3 digit
                'nisn' => 'TMP' . time() . rand(100, 999), 
                'kelas' => $kelasValue,
                'rombel' => $kelasValue,
                'no_hp_orangtua' => $request->filled('no_hp_orangtua') ? trim(strip_tags($request->no_hp_orangtua)) : '-',
            ]);

            // Auto-enroll student to Rombel & Ekstrakurikuler Program if rombel ID provided
            if ($request->filled('ekstrakurikuler_rombel_id')) {
                $rombel = \App\Models\EkstrakurikulerRombel::find($request->ekstrakurikuler_rombel_id);
                if ($rombel) {
                    $isEnrolled = $rombel->siswa()->where('siswa_id', $student->id)->exists();
                    if (!$isEnrolled) {
                        $rombel->siswa()->syncWithoutDetaching([
                            $student->id => [
                                'ekstrakurikuler_id' => $rombel->ekstrakurikuler_id,
                                'status' => 'aktif',
                                'tanggal_daftar' => now(),
                                'catatan' => 'Auto-enrolled via Quick Add Student',
                            ]
                        ]);
                    }
                }
            }

            // Log activity for mitigation/audit
            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'create_student_manual',
                'description' => "Instruktur menambahkan siswa baru manual: {$student->nama_lengkap}",
                'subject_type' => \App\Models\Siswa::class,
                'subject_id' => $student->id,
                'properties' => ['ip' => $request->ip(), 'agent' => $request->userAgent()]
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $student->id,
                    'nama_lengkap' => $student->nama_lengkap,
                    'sekolah_kodlan' => $student->sekolah_kodlan,
                ],
                'message' => 'Siswa berhasil ditambahkan'
            ]);

        } catch (\Exception $e) {
            Log::error('Error creating quick student', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan siswa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get active students from other parallel rombels within the same extracurricular program.
     */
    public function getParallelRombelStudents(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'ekstrakurikuler_id' => 'required|exists:ekstrakurikuler,id',
                'current_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
            ]);

            $ekskulId = $request->ekstrakurikuler_id;
            $currentRombelId = $request->current_rombel_id;

            // Fetch parallel rombels in the same program
            $parallelRombels = \App\Models\EkstrakurikulerRombel::where('ekstrakurikuler_id', $ekskulId)
                ->where('id', '!=', $currentRombelId)
                ->with(['activeEnrollments.siswa' => function($q) {
                    $q->select('id', 'nama_lengkap', 'jenis_kelamin', 'kelas', 'rombel');
                }])
                ->get();

            $result = [];
            foreach ($parallelRombels as $rombel) {
                $students = [];
                foreach ($rombel->activeEnrollments as $enrollment) {
                    if ($enrollment->siswa) {
                        $students[] = [
                            'siswa_id' => $enrollment->siswa->id,
                            'nama_lengkap' => $enrollment->siswa->nama_lengkap,
                            'jenis_kelamin' => $enrollment->siswa->jenis_kelamin,
                            'kelas' => $enrollment->siswa->kelas ?? $enrollment->siswa->rombel ?? '-',
                            'source_rombel_id' => $rombel->id,
                            'source_rombel_nama' => $rombel->nama_rombel,
                        ];
                    }
                }

                $result[] = [
                    'rombel_id' => $rombel->id,
                    'nama_rombel' => $rombel->nama_rombel,
                    'hari' => ucfirst($rombel->hari ?? '-'),
                    'jam' => ($rombel->jam_mulai ? \Carbon\Carbon::parse($rombel->jam_mulai)->format('H:i') : '-') . ' - ' . ($rombel->jam_selesai ? \Carbon\Carbon::parse($rombel->jam_selesai)->format('H:i') : '-'),
                    'total_siswa' => count($students),
                    'students' => $students,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching parallel students', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat daftar siswa rombel lain',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Transfer student to target rombel (multi-rombel, vice-versa, with grayed-out handling).
     */
    public function transferStudentToRombel(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'target_rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
                'ekstrakurikuler_id' => 'required|exists:ekstrakurikuler,id',
                'alasan' => 'nullable|string|max:500',
            ]);

            $siswaId = $request->siswa_id;
            $targetRombelId = $request->target_rombel_id;
            $ekskulId = $request->ekstrakurikuler_id;
            $alasan = $request->alasan ?? 'Pindah rombel saat sesi mengajar';

            $targetRombel = \App\Models\EkstrakurikulerRombel::findOrFail($targetRombelId);
            $siswa = \App\Models\Siswa::findOrFail($siswaId);

            \Illuminate\Support\Facades\DB::transaction(function () use ($siswaId, $targetRombelId, $ekskulId, $alasan, $targetRombel) {
                // 1. Cari seluruh enrollment aktif siswa di ekskul ini dan ubah statusnya menjadi pindah
                $activeEnrollments = \App\Models\SiswaEkstrakurikuler::where('siswa_id', $siswaId)
                    ->where('ekstrakurikuler_id', $ekskulId)
                    ->where('ekstrakurikuler_rombel_id', '!=', $targetRombelId)
                    ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF)
                    ->get();

                foreach ($activeEnrollments as $oldEnrollment) {
                    $oldEnrollment->update([
                        'status' => \App\Models\SiswaEkstrakurikuler::STATUS_PINDAH,
                        'tanggal_keluar' => now(),
                        'alasan_keluar' => "Pindah ke {$targetRombel->nama_rombel}",
                        'catatan' => "Pindah ke Rombel ID: {$targetRombelId}. {$alasan}",
                        'updated_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }

                // 2. Aktifkan atau buat enrollment di target rombel
                $targetEnrollment = \App\Models\SiswaEkstrakurikuler::where('siswa_id', $siswaId)
                    ->where('ekstrakurikuler_id', $ekskulId)
                    ->where('ekstrakurikuler_rombel_id', $targetRombelId)
                    ->first();

                if ($targetEnrollment) {
                    $targetEnrollment->update([
                        'status' => \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF,
                        'tanggal_daftar' => now(),
                        'tanggal_keluar' => null,
                        'alasan_keluar' => null,
                        'catatan' => "Dipindahkan/Diaktifkan kembali di {$targetRombel->nama_rombel}. {$alasan}",
                        'updated_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                } else {
                    \App\Models\SiswaEkstrakurikuler::create([
                        'siswa_id' => $siswaId,
                        'ekstrakurikuler_id' => $ekskulId,
                        'ekstrakurikuler_rombel_id' => $targetRombelId,
                        'status' => \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF,
                        'tanggal_daftar' => now(),
                        'catatan' => "Pindahan ke {$targetRombel->nama_rombel}. {$alasan}",
                        'created_by' => \Illuminate\Support\Facades\Auth::id(),
                    ]);
                }
            });

            // Activity Log
            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'transfer_student_rombel',
                'description' => "Memindahkan siswa {$siswa->nama_lengkap} ke {$targetRombel->nama_rombel}",
                'subject_type' => \App\Models\Siswa::class,
                'subject_id' => $siswa->id,
                'properties' => [
                    'target_rombel_id' => $targetRombelId,
                    'target_rombel_nama' => $targetRombel->nama_rombel,
                    'alasan' => $alasan
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Siswa {$siswa->nama_lengkap} berhasil dipindahkan ke {$targetRombel->nama_rombel}",
                'data' => [
                    'siswa_id' => $siswa->id,
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'jenis_kelamin' => $siswa->jenis_kelamin,
                    'target_rombel_id' => $targetRombel->id,
                    'target_rombel_nama' => $targetRombel->nama_rombel,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error transferring student to rombel', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memindahkan siswa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Withdraw student from current rombel (quick exit).
     */
    public function withdrawStudent(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'siswa_id' => 'required|exists:siswa,id',
                'rombel_id' => 'required|exists:ekstrakurikuler_rombel,id',
                'alasan_keluar' => 'required|string|max:500',
            ]);

            $siswaId = $request->siswa_id;
            $rombelId = $request->rombel_id;
            $alasan = $request->alasan_keluar;

            $rombel = \App\Models\EkstrakurikulerRombel::findOrFail($rombelId);
            $siswa = \App\Models\Siswa::findOrFail($siswaId);

            $enrollment = \App\Models\SiswaEkstrakurikuler::where('siswa_id', $siswaId)
                ->where('ekstrakurikuler_rombel_id', $rombelId)
                ->where('status', \App\Models\SiswaEkstrakurikuler::STATUS_AKTIF)
                ->first();

            if (!$enrollment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Siswa tidak ditemukan atau sudah tidak aktif di rombel ini.'
                ], 404);
            }

            $enrollment->update([
                'status' => \App\Models\SiswaEkstrakurikuler::STATUS_KELUAR,
                'tanggal_keluar' => now(),
                'alasan_keluar' => $alasan,
                'updated_by' => \Illuminate\Support\Facades\Auth::id(),
            ]);

            // Activity Log
            \App\Models\ActivityLog::create([
                'user_id' => \Illuminate\Support\Facades\Auth::id(),
                'action' => 'withdraw_student_rombel',
                'description' => "Mengeluarkan siswa {$siswa->nama_lengkap} dari {$rombel->nama_rombel} (Alasan: {$alasan})",
                'subject_type' => \App\Models\Siswa::class,
                'subject_id' => $siswa->id,
                'properties' => [
                    'rombel_id' => $rombelId,
                    'alasan' => $alasan
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => "Siswa {$siswa->nama_lengkap} berhasil dikeluarkan dari {$rombel->nama_rombel}",
                'data' => [
                    'siswa_id' => $siswa->id,
                    'nama_lengkap' => $siswa->nama_lengkap,
                    'rombel_id' => $rombel->id,
                    'rombel_nama' => $rombel->nama_rombel,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error withdrawing student', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengeluarkan siswa',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
<?php

namespace App\Services\Ekstrakurikuler;

use App\Models\Ekstrakurikuler;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EkstrakurikulerQueryService
{
    /**
     * Build query with filters
     */
    public function buildFilteredQuery(Request $request, $user)
    {
        $query = Ekstrakurikuler::with(['sekolah', 'sales', 'rombels']);

        // Filter out Ad-Hoc / Special Activity programs (Trial Class, Sosialisasi Sales, Pameran, Event, Backup Pertemuan, Remedial, Inkul)
        // Main /ekstrakurikuler page should only display official regular contract programs
        if (! $request->filled('include_adhoc')) {
            $query->whereNotIn('ekstrakurikuler.kategori_program', [
                'Trial Class',
                'Sosialisasi bersama Sales',
                'Sosialisasi',
                'Free Trial',
                'Pameran',
                'Event',
                'Ad-Hoc',
                'Kegiatan Khusus',
                'Backup Pertemuan',
                'Remedial',
                'Inkul',
                'In-Kurikuler',
                'Inkul Coding Scratch',
            ])
            ->where('ekstrakurikuler.kategori_program', 'not like', '%Trial%')
            ->where('ekstrakurikuler.kategori_program', 'not like', '%Sosialisasi%')
            ->where('ekstrakurikuler.kategori_program', 'not like', '%Backup%')
            ->where('ekstrakurikuler.kategori_program', 'not like', '%Remedial%')
            ->where('ekstrakurikuler.kategori_program', 'not like', '%Inkul%');
        }

        // Filter by user role
        if ($user->hasRole('instruktur')) {
            // Instruktur hanya melihat ekstrakurikuler yang punya rombel dimana mereka ditugaskan
            $query->whereHas('rombels', function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        }

        // Apply filters
        $this->applyFilters($query, $request);

        return $query;
    }

    /**
     * Apply various filters to the query
     */
    protected function applyFilters($query, Request $request)
    {
        if ($request->filled('status')) {
            $query->where('ekstrakurikuler.status', $request->status);
        }

        if ($request->filled('region')) {
            $cities = app(\App\Services\Ekstrakurikuler\RegionMappingService::class)->getCitiesByRegion($request->region);
            $query->where(function ($q) use ($request, $cities) {
                $q->where('ekstrakurikuler.region', $request->region)
                  ->orWhereHas('sekolah', function ($subQ) use ($cities) {
                      $subQ->whereIn('kota', $cities->toArray());
                  });
            });
        }

        if ($request->filled('kota')) {
            $query->whereHas('sekolah', function ($q) use ($request) {
                $q->where('kota', $request->kota);
            });
        }

        if ($request->filled('sekolah_kodlan')) {
            $query->where('ekstrakurikuler.sekolah_kodlan', $request->sekolah_kodlan);
        }

        $this->applySearchFilter($query, $request);
        $this->applyDateFilter($query, $request);
        $this->applySorting($query, $request);
    }

    /**
     * Apply sorting filter
     */
    protected function applySorting($query, Request $request)
    {
        $sort = $request->input('sort', 'priority');

        switch ($sort) {
            case 'oldest':
                $query->orderBy('ekstrakurikuler.created_at', 'asc');
                break;
            case 'school_asc':
                $query->join('sekolah', 'ekstrakurikuler.sekolah_kodlan', '=', 'sekolah.kodlan')
                      ->orderBy('sekolah.namasekolah', 'asc')
                      ->select('ekstrakurikuler.*');
                break;
            case 'school_desc':
                $query->join('sekolah', 'ekstrakurikuler.sekolah_kodlan', '=', 'sekolah.kodlan')
                      ->orderBy('sekolah.namasekolah', 'desc')
                      ->select('ekstrakurikuler.*');
                break;
            case 'program_asc':
                $query->orderBy('ekstrakurikuler.kategori_program', 'asc');
                break;
            case 'status_asc':
                $query->orderBy('ekstrakurikuler.status', 'asc');
                break;
            case 'latest_created':
                $query->orderBy('ekstrakurikuler.created_at', 'desc');
                break;
            case 'priority':
            case 'latest':
            default:
                // Opsi A: Prioritas Status (Aktif -> Draf -> Selesai -> Dibatalkan) -> Nama Sekolah A-Z -> Terbaru
                $query->leftJoin('sekolah', 'ekstrakurikuler.sekolah_kodlan', '=', 'sekolah.kodlan')
                      ->orderByRaw("CASE 
                            WHEN ekstrakurikuler.status = 'aktif' THEN 1 
                            WHEN ekstrakurikuler.status = 'draf' THEN 2 
                            WHEN ekstrakurikuler.status = 'selesai' THEN 3 
                            ELSE 4 
                        END ASC")
                      ->orderBy('sekolah.namasekolah', 'asc')
                      ->orderBy('ekstrakurikuler.created_at', 'desc')
                      ->select('ekstrakurikuler.*');
                break;
        }
    }

    /**
     * Apply search filter
     */
    protected function applySearchFilter($query, Request $request)
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('ekstrakurikuler.kategori_program', 'like', "%{$searchTerm}%")
                    ->orWhereHas('sekolah', function ($subQuery) use ($searchTerm) {
                        $subQuery->where('namasekolah', 'like', "%{$searchTerm}%");
                    });
            });
        }
    }

    /**
     * Apply date range filter
     */
    protected function applyDateFilter($query, Request $request)
    {
        if ($request->filled('date_range')) {
            try {
                $dateRange = str_replace(' - ', ' to ', $request->date_range);
                $dates = array_map('trim', explode(' to ', $dateRange));

                if (count($dates) === 2) {
                    $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                    $query->whereBetween('tanggal_mulai', [$startDate, $endDate]);
                }
            } catch (\Exception $e) {
                Log::warning('Invalid date range format', ['date_range' => $request->date_range]);
            }
        }
    }

    /**
     * Get filter options for the index page
     */
    public function getFilterOptions()
    {
        return [
            'sekolahs' => Sekolah::select('kodlan', 'namasekolah', 'kotkab')
                ->orderBy('namasekolah')
                ->get(),
            'statuses' => [
                Ekstrakurikuler::STATUS_DRAFT => 'Draft',
                Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
                Ekstrakurikuler::STATUS_DISETUJUI => 'Disetujui',
                Ekstrakurikuler::STATUS_DITOLAK => 'Ditolak',
                Ekstrakurikuler::STATUS_AKTIF => 'Aktif',
                Ekstrakurikuler::STATUS_SELESAI => 'Selesai',
                Ekstrakurikuler::STATUS_DIBATALKAN => 'Dibatalkan',
            ],
        ];
    }

    /**
     * Calculate statistics
     */
    public function getStatistics()
    {
        return [
            'total' => Ekstrakurikuler::count(),
            'aktif' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_AKTIF)->count(),
            'diajukan' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_DIAJUKAN)->count(),
            'selesai' => Ekstrakurikuler::where('status', Ekstrakurikuler::STATUS_SELESAI)->count(),
        ];
    }

    /**
     * Get form creation data
     */
    public function getFormCreationData()
    {
        return [
            'sekolahs' => collect(), // Performance optimization: schools are loaded via AJAX/Select2
            'salesUsers' => \App\Models\Salesman::orderBy('nama_salesman')
                ->get()
                ->map(function ($salesman) {
                    return (object) [
                        'id' => $salesman->id,
                        'nama_lengkap' => $salesman->nama_salesman,
                        'role' => 'Salesman',
                        'division' => (object) ['name' => $salesman->area ?? 'General']
                    ];
                }),
            'statuses' => [
                Ekstrakurikuler::STATUS_DRAFT => 'Draft',
                Ekstrakurikuler::STATUS_DIAJUKAN => 'Diajukan',
            ],
            'activeProducts' => \App\Models\Product::where('is_aktif', true)->orderBy('nama_produk')->get(),
        ];
    }

    /**
     * Get schools by city for API
     */
    public function getSchoolsByCity(string $kota)
    {
        if (!$kota) {
            return collect();
        }

        return Sekolah::where('kota', $kota)
            ->select('kodlan', 'namasekolah', 'kotkab', 'kec')
            ->orderBy('namasekolah')
            ->get();
    }

    /**
     * Get city from school kodlan
     */
    public function getCityFromSchool(string $kodlan)
    {
        if (!$kodlan) {
            return null;
        }

        $sekolah = Sekolah::find($kodlan);
        return $sekolah ? $sekolah->kota : null;
    }
}
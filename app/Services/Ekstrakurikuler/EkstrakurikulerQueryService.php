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

        // Filter by user role
        if ($user->hasRole('instruktur')) {
            // Instruktur hanya melihat ekstrakurikuler yang punya rombel dimana mereka ditugaskan
            $query->whereHas('rombels', function ($q) use ($user) {
                $q->where('user_id_instruktur', $user->id)
                  ->orWhere('user_id_asisten', $user->id);
            });
        } elseif (! $user->hasAdminAccess()) {
            $query->where('user_id_sales', $user->id);
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
            $query->where('status', $request->status);
        }

        if ($request->filled('region')) {
            $cities = app(\App\Services\Ekstrakurikuler\RegionMappingService::class)->getCitiesByRegion($request->region);
            $query->where(function ($q) use ($request, $cities) {
                $q->where('region', $request->region)
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
            $query->where('sekolah_kodlan', $request->sekolah_kodlan);
        }

        $this->applySearchFilter($query, $request);
        $this->applyDateFilter($query, $request);
    }

    /**
     * Apply search filter
     */
    protected function applySearchFilter($query, Request $request)
    {
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('kategori_program', 'like', "%{$searchTerm}%")
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
            'salesUsers' => User::with('division')
                ->whereIn('role', ['sales', 'koordinator'])
                ->orderBy('nama_lengkap')
                ->get(),
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
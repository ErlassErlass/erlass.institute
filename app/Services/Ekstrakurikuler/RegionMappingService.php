<?php

namespace App\Services\Ekstrakurikuler;

use App\Models\Sekolah;
use Illuminate\Support\Collection;

/**
 * Service untuk menangani mapping city ke region dan logic terkait geografi
 */
class RegionMappingService
{
    /**
     * Mapping city ke region yang sudah didefinisikan
     */
    protected array $cityToRegionMap = [
        'JAKARTA PUSAT' => 'JAKARTA',
        'JAKARTA UTARA' => 'JAKARTA',
        'JAKARTA SELATAN' => 'JAKARTA',
        'JAKARTA TIMUR' => 'JAKARTA',
        'JAKARTA BARAT' => 'JAKARTA',
        'KOTA DEPOK' => 'DEPOK',
        'KABUPATEN BOGOR' => 'BOGOR',
        'KOTA BOGOR' => 'BOGOR',
        'KOTA TANGERANG' => 'TANGERANG',
        'KABUPATEN TANGERANG' => 'TANGERANG',
        'KOTA TANGERANG SELATAN' => 'TANGERANG',
        'KOTA BEKASI' => 'BEKASI',
        'KABUPATEN BEKASI' => 'BEKASI',
    ];

    /**
     * Dapatkan mapping city ke region
     */
    public function getCityToRegionMapping(): array
    {
        return $this->cityToRegionMap;
    }

    /**
     * Map city ke region
     */
    public function mapCityToRegion(string $city): string
    {
        return $this->cityToRegionMap[$city] ?? $this->extractRegionFromCity($city);
    }

    /**
     * Dapatkan semua region yang tersedia berdasarkan data kota aktual
     */
    public function getAvailableRegions(): array
    {
        $allKota = Sekolah::select('kota')
            ->distinct()
            ->whereNotNull('kota')
            ->orderBy('kota')
            ->pluck('kota');

        $regions = $allKota->map(function ($kota) {
            return $this->mapCityToRegion($kota);
        })->unique()->sort()->values()->toArray();

        return $regions;
    }

    /**
     * Dapatkan cities yang tersedia untuk dropdown
     */
    public function getAvailableCities(): array
    {
        return Sekolah::select('kota')
            ->distinct()
            ->whereNotNull('kota')
            ->orderBy('kota')
            ->pluck('kota')
            ->toArray();
    }

    /**
     * Dapatkan region berdasarkan sekolah kodlan
     */
    public function getRegionFromSekolah(string $kodlan): ?string
    {
        $sekolah = Sekolah::find($kodlan);
        
        if (!$sekolah || !$sekolah->kota) {
            return null;
        }

        return $this->mapCityToRegion($sekolah->kota);
    }

    /**
     * Validasi apakah region valid
     */
    public function isValidRegion(string $region): bool
    {
        $validRegions = ['JAKARTA', 'DEPOK', 'BOGOR', 'TANGERANG', 'BEKASI'];
        return in_array(strtoupper($region), $validRegions);
    }

    /**
     * Dapatkan cities berdasarkan region
     */
    public function getCitiesByRegion(string $region): Collection
    {
        $regionUpper = strtoupper($region);
        $mappingFlipped = array_flip($this->cityToRegionMap);
        
        $cities = collect();
        
        foreach ($this->cityToRegionMap as $city => $mappedRegion) {
            if ($mappedRegion === $regionUpper) {
                $cities->push($city);
            }
        }

        // Juga cari cities yang belum ada mapping tapi cocok dengan region
        $allCities = $this->getAvailableCities();
        foreach ($allCities as $city) {
            if (!isset($this->cityToRegionMap[$city])) {
                $extractedRegion = $this->extractRegionFromCity($city);
                if ($extractedRegion === $regionUpper) {
                    $cities->push($city);
                }
            }
        }

        return $cities->unique()->sort()->values();
    }

    /**
     * Extract region dari nama city jika tidak ada mapping eksplisit
     */
    protected function extractRegionFromCity(string $city): string
    {
        // Logic untuk extract region dari nama city
        // Contoh: "KOTA BANDUNG" -> "BANDUNG"
        $cityParts = explode(' ', $city);
        
        if (count($cityParts) > 1) {
            return strtoupper($cityParts[1] ?? $cityParts[0]);
        }
        
        return strtoupper($city);
    }

    /**
     * Dapatkan statistik region
     */
    public function getRegionStatistics(): array
    {
        $stats = [];
        $regions = $this->getAvailableRegions();

        foreach ($regions as $region) {
            $cities = $this->getCitiesByRegion($region);
            $schoolCount = Sekolah::whereIn('kota', $cities->toArray())->count();
            
            $stats[$region] = [
                'cities_count' => $cities->count(),
                'schools_count' => $schoolCount,
                'cities' => $cities->toArray(),
            ];
        }

        return $stats;
    }

    /**
     * Normalisasi nama city untuk konsistensi
     */
    public function normalizeCityName(string $city): string
    {
        return strtoupper(trim($city));
    }

    /**
     * Dapatkan region default berdasarkan user atau konfigurasi
     */
    public function getDefaultRegion(): ?string
    {
        // Bisa disesuaikan berdasarkan user profile atau konfigurasi sistem
        return 'JAKARTA'; // Default region
    }
}
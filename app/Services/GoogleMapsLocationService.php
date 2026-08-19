<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class GoogleMapsLocationService
{
    /**
     * Radius toleransi default (dalam meter).
     */
    public const DEFAULT_RADIUS_TOLERANCE_METERS = 500;

    /**
     * Ekstrak latitude & longitude dari URL Google Maps (baik short link maupun long link).
     *
     * @param string|null $url
     * @return array{lat: float, lng: float, source: string}|null
     */
    public function extractCoordinates(?string $url): ?array
    {
        if (empty($url)) {
            return null;
        }

        // Hapus semua whitespace & newline tersembunyi yang mungkin terbawa saat copy-paste
        $url = preg_replace('/\s+/', '', trim((string) $url));
        if (empty($url)) {
            return null;
        }

        // Jika URL sudah mengandung koordinat tanpa perlu redirect
        $coords = $this->parseCoordinatesFromUrlString($url);
        if ($coords) {
            return $coords;
        }

        // Jika short link (maps.app.goo.gl, share.google, goo.gl, bit.ly, dll), lakukan resolution URL
        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HEADER => true,
                CURLOPT_NOBODY => true,
                CURLOPT_TIMEOUT => 3,
                CURLOPT_CONNECTTIMEOUT => 2,
                CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            curl_exec($ch);
            $effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) ?: $url;
            curl_close($ch);

            return $this->parseCoordinatesFromUrlString($effectiveUrl);
        } catch (\Throwable $e) {
            Log::warning("Gagal meresolve koordinat dari Google Maps URL: {$url}. Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Parse koordinat dari string URL yang sudah di-resolve.
     *
     * @param string $url
     * @return array{lat: float, lng: float, source: string}|null
     */
    protected function parseCoordinatesFromUrlString(string $url): ?array
    {
        // 1. Pola Pin spesifik: !3d<lat>!4d<lng>
        if (preg_match('/!3d(-?\d+(?:\.\d+)?)(?:!|%21)4d(-?\d+(?:\.\d+)?)/i', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
                'source' => 'pin',
            ];
        }

        // 2. Pola Query parameter: ?q=<lat>,<lng> atau &q=<lat>,<lng>
        if (preg_match('/[?&]q=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/i', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
                'source' => 'query',
            ];
        }

        // 3. Pola Center View: @<lat>,<lng>
        if (preg_match('/@(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/i', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
                'source' => 'center',
            ];
        }

        // 4. Pola ll parameter: ?ll=<lat>,<lng>
        if (preg_match('/[?&]ll=(-?\d+(?:\.\d+)?),(-?\d+(?:\.\d+)?)/i', $url, $m)) {
            return [
                'lat' => (float) $m[1],
                'lng' => (float) $m[2],
                'source' => 'll',
            ];
        }

        return null;
    }

    /**
     * Hitung jarak dua titik koordinat GPS menggunakan formula Haversine (dalam satuan meter).
     *
     * @param float $lat1
     * @param float $lng1
     * @param float $lat2
     * @param float $lng2
     * @return int Jarak dalam meter
     */
    public function calculateDistance(float $lat1, float $lng1, float $lat2, float $lng2): int
    {
        $earthRadius = 6371000; // Radius bumi dalam meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round($earthRadius * $c);
    }
}

<?php

namespace App\Services;

class PhoneNumberService
{
    /**
     * Normalisasi nomor telepon Indonesia ke format murni 628xxx (tanpa +, spasi, strip).
     *
     * Contoh:
     *   0812-3456-7890   → 6281234567890
     *   +62 812 3456 78  → 6281234567890
     *   6281234567890    → 6281234567890
     *   081234           → null (nomor tidak valid / kurang dari 10 digit)
     *
     * @param  string|null  $phone
     * @return string|null  null jika nomor kosong atau format tidak valid
     */
    public static function normalize(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Hapus semua karakter selain angka (digit)
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // Konversi prefix nomor HP Indonesia
        if (str_starts_with($digits, '62')) {
            $normalized = $digits;
        } elseif (str_starts_with($digits, '0')) {
            // Convert 08xxx → 628xxx
            $normalized = '62' . substr($digits, 1);
        } else {
            // Tanpa prefix 0 atau 62, asumsikan nomor lokal Indonesia
            $normalized = '62' . $digits;
        }

        // Validasi panjang nomor WA Indonesia: 62 + 8-12 digit = 10-15 digit total
        if (strlen($normalized) < 10 || strlen($normalized) > 15) {
            return null;
        }

        return $normalized;
    }
}

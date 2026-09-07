<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstructorProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'waktu_mengajar' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Master list of normalized instructor domicile cities.
     */
    public static function listKotaDomisili(): array
    {
        return [
            'Jakarta Selatan',
            'Jakarta Timur',
            'Jakarta Pusat',
            'Jakarta Barat',
            'Jakarta Utara',
            'Kota Bogor',
            'Kabupaten Bogor',
            'Depok',
            'Kota Tangerang',
            'Kabupaten Tangerang',
            'Tangerang Selatan',
            'Kota Bekasi',
            'Kabupaten Bekasi',
            'Serang',
            'Lainnya',
        ];
    }

    /**
     * Master list of standardized bank abbreviations.
     */
    public static function listNamaBank(): array
    {
        return [
            'BCA',
            'MANDIRI',
            'BRI',
            'BNI',
            'BSI',
            'SEABANK',
            'BTN',
            'JAGO',
            'BCA DIGITAL',
            'CIMB',
            'PERMATA',
            'DANAMON',
            'MAYBANK',
            'OCBC',
            'BTPN',
            'KROM',
            'DANA',
            'LAINNYA',
        ];
    }

    /**
     * Normalize any variation of bank name into its standard abbreviation.
     */
    public static function normalizeBankName(?string $name): ?string
    {
        if (!$name) {
            return null;
        }

        $clean = trim($name);
        if ($clean === '' || $clean === '-') {
            return '-';
        }

        $upper = strtoupper($clean);

        // Map variations to standard abbreviation
        if (str_contains($upper, 'BCA') && (str_contains($upper, 'BLU') || str_contains($upper, 'DIGITAL'))) {
            return 'BCA DIGITAL';
        }
        if ($upper === 'BLU' || str_contains($upper, 'BLU') || str_contains($upper, 'BCA DIGITAL')) {
            return 'BCA DIGITAL';
        }
        if (str_contains($upper, 'BCA') || str_contains($upper, 'CENTRAL ASIA')) {
            return 'BCA';
        }
        if (str_contains($upper, 'MANDIRI')) {
            return 'MANDIRI';
        }
        if (str_contains($upper, 'BNI') || str_contains($upper, 'NEGARA INDONESIA')) {
            return 'BNI';
        }
        if (str_contains($upper, 'BRI') || str_contains($upper, 'RAKYAT INDONESIA')) {
            return 'BRI';
        }
        if (str_contains($upper, 'BSI') || str_contains($upper, 'SYARIAH INDONESIA')) {
            return 'BSI';
        }
        if (str_contains($upper, 'SEABANK') || str_contains($upper, 'SEBANK') || str_contains($upper, 'SEA BANK')) {
            return 'SEABANK';
        }
        if (str_contains($upper, 'BTN') || str_contains($upper, 'TABUNGAN NEGARA')) {
            return 'BTN';
        }
        if (str_contains($upper, 'JAGO')) {
            return 'JAGO';
        }
        if (str_contains($upper, 'CIMB') || str_contains($upper, 'NIAGA')) {
            return 'CIMB';
        }
        if (str_contains($upper, 'PERMATA')) {
            return 'PERMATA';
        }
        if (str_contains($upper, 'DANAMON')) {
            return 'DANAMON';
        }
        if (str_contains($upper, 'MAYBANK') || str_contains($upper, 'BII')) {
            return 'MAYBANK';
        }
        if (str_contains($upper, 'OCBC') || str_contains($upper, 'NISP')) {
            return 'OCBC';
        }
        if (str_contains($upper, 'BTPN') || str_contains($upper, 'JENIUS')) {
            return 'BTPN';
        }
        if (str_contains($upper, 'KROM')) {
            return 'KROM';
        }
        if (str_contains($upper, 'DANA')) {
            return 'DANA';
        }

        return $upper;
    }

    /**
     * Mutator to automatically clean and sanitize nomor rekening.
     * Menghapus semua karakter non-angka (spasi, minus, titik, dsb).
     */
    public function setNoRekeningAttribute($value): void
    {
        if ($value === null || trim((string)$value) === '' || trim((string)$value) === '-') {
            $this->attributes['no_rekening'] = null;
        } else {
            $digitsOnly = preg_replace('/[^0-9]/', '', (string)$value);
            $this->attributes['no_rekening'] = $digitsOnly !== '' ? $digitsOnly : null;
        }
    }

    /**
     * Mutator to automatically normalize nama_bank into uppercase abbreviation.
     */
    public function setNamaBankAttribute($value): void
    {
        $this->attributes['nama_bank'] = self::normalizeBankName($value);
    }
}

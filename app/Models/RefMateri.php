<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefMateri extends Model
{
    public $timestamps = false;
    protected $table = 'ref_materi';
    protected $fillable = ['kategori', 'materi'];

    /**
     * Map kategori varian/alias ke kategori utama
     */
    public static array $categoryMap = [
        // Coding Scratch
        'Coding Scratch' => 'Coding Scratch',
        'Ekskul Coding Scratch' => 'Coding Scratch',
        'Pelatihan Coding Scratch' => 'Coding Scratch',

        // English Course
        'English Course' => 'English Course',
        'Ekskul English Course' => 'English Course',
        'Pelatihan English Course' => 'English Course',

        // Pictoblox AI
        'Pictoblox AI' => 'Pictoblox AI',
        'Ekskul Pictoblox AI' => 'Pictoblox AI',
        'Pelatihan Pictoblox AI' => 'Pictoblox AI',

        // Robotika Jimu
        'Robotika Jimu' => 'Robotika Jimu',
        'Robotik Jimu' => 'Robotika Jimu',
        'Ekskul Robotik Jimu' => 'Robotika Jimu',
        'Ekskul Robotika Jimu' => 'Robotika Jimu',
        'Pelatihan Robotik Jimu' => 'Robotika Jimu',
        'Pelatihan Robotika Jimu' => 'Robotika Jimu',

        // Robotika micro:bit
        'Robotika micro:bit' => 'Robotika micro:bit',
        'Robotik micro:bit' => 'Robotika micro:bit',
        'Micro:bit Learning Kit' => 'Robotika micro:bit',
        'Robotik Microbit Learning Kit' => 'Robotika micro:bit',
        'Ekskul Robotik Microbit Learning Kit' => 'Robotika micro:bit',
        'Ekskul Robotika micro:bit' => 'Robotika micro:bit',
        'Pelatihan Robotik Microbit Learning Kit' => 'Robotika micro:bit',
        'Pelatihan Robotik Microbit Beginner' => 'Robotika micro:bit',
        'Pelatihan Robotik micro:bit' => 'Robotika micro:bit',
        'Pelatihan Robotika micro:bit' => 'Robotika micro:bit',
        'Pelatihan Robotika Microbit Learning Kit' => 'Robotika micro:bit',

        // Robotika Robotic Explorer
        'Robotika Robotic Explorer' => 'Robotika Robotic Explorer',
        'Robotik Explorer' => 'Robotika Robotic Explorer',
        'Robotik Robotic Explorer' => 'Robotika Robotic Explorer',
        'Ekskul Robotik Explorer' => 'Robotika Robotic Explorer',
        'Ekskul Robotika Robotic Explorer' => 'Robotika Robotic Explorer',
        'Pelatihan Robotik Explorer' => 'Robotika Robotic Explorer',
        'Pelatihan Robotika Explorer' => 'Robotika Robotic Explorer',
        'Pelatihan Robotik Robotic Explorer' => 'Robotika Robotic Explorer',
        'Pelatihan Robotika Robotic Explorer' => 'Robotika Robotic Explorer',

        // Erboblox
        'Erboblox' => 'Erboblox',
        'Robotik Erboblox' => 'Erboblox',
        'Ekskul Erboblox' => 'Erboblox',
        'Ekskul Robotik Erboblox' => 'Erboblox',
        'Robotika Erboblox' => 'Erboblox',
        'Ekskul Robotika Erboblox' => 'Erboblox',
        'Pelatihan Erboblox' => 'Erboblox',
        'Pelatihan Robotik Erboblox' => 'Erboblox',
        'Pelatihan Robotika Erboblox' => 'Erboblox',

        // Pameran
        'Pameran' => 'Pameran',
        'Ekskul Pameran' => 'Pameran',
        'Pelatihan Pameran' => 'Pameran',

        // Pendampingan Lomba
        'Pendampingan Lomba' => 'Pendampingan Lomba',
        'Ekskul Pendampingan Lomba' => 'Pendampingan Lomba',
        'Pelatihan Pendampingan Lomba' => 'Pendampingan Lomba',
    ];

    /**
     * Resolve base category name from any alias or prefix (e.g. Pelatihan, Ekskul).
     */
    public static function resolveCategory(?string $kategori): ?string
    {
        if (!$kategori) {
            return null;
        }

        $trimmed = trim($kategori);
        if (isset(self::$categoryMap[$trimmed])) {
            return self::$categoryMap[$trimmed];
        }

        // Coba bersihkan prefix "Pelatihan " atau "Ekskul " jika ada variasi lain
        $stripped = preg_replace('/^(Pelatihan|Ekskul)\s+/i', '', $trimmed);
        if (isset(self::$categoryMap[$stripped])) {
            return self::$categoryMap[$stripped];
        }

        return $trimmed;
    }

    /**
     * Ambil daftar materi berdasarkan kategori program, dengan fallback cerdas ke base category.
     */
    public static function getMateriList(?string $kategori)
    {
        if (!$kategori) {
            return collect();
        }

        // 1. Coba cari langsung dengan kategori yang diberikan
        $list = static::where('kategori', $kategori)
            ->orderByRaw("CASE WHEN TRIM(materi) = 'Lain - Lain' THEN 1 ELSE 0 END")
            ->orderBy('id', 'asc')
            ->pluck('materi');

        if ($list->isNotEmpty()) {
            return $list;
        }

        // 2. Jika kosong, cari berdasarkan kategori hasil mapping
        $resolved = static::resolveCategory($kategori);
        if ($resolved && $resolved !== $kategori) {
            $list = static::where('kategori', $resolved)
                ->orderByRaw("CASE WHEN TRIM(materi) = 'Lain - Lain' THEN 1 ELSE 0 END")
                ->orderBy('id', 'asc')
                ->pluck('materi');

            if ($list->isNotEmpty()) {
                return $list;
            }
        }

        return collect();
    }
}

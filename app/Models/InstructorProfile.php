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
}

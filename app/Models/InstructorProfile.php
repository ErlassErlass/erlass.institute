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
}

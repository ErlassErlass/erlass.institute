<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateReportRequest extends Model
{
    protected $fillable = ['user_id', 'session_id', 'adhoc_date', 'reason', 'status', 'admin_id', 'admin_notes'];

    protected $casts = [
        'adhoc_date' => 'date',
    ];

    public function isAdhoc(): bool
    {
        return empty($this->session_id) && !empty($this->adhoc_date);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function session()
    {
        return $this->belongsTo(EkstrakurikulerSession::class, 'session_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

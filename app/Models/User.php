<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // ADD THIS
use Illuminate\Notifications\Notifiable;

// ADD THIS LINE

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The "booted" method of the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Default values for fields that are missing
            if (empty($user->status)) {
                $user->status = 'Aktif';
            }

            // Automate instructor_id for new instructors
            if ($user->role === 'instruktur' && empty($user->instructor_id)) {
                $user->instructor_id = static::generateInstructorId();
            }

            if (empty($user->tanggal_lahir)) {
                $user->tanggal_lahir = '1990-01-01';
            }
            if (empty($user->agama)) {
                $user->agama = 'Lainnya';
            }
            if (empty($user->pend_terakhir)) {
                $user->pend_terakhir = 'SMA';
            }
            if (empty($user->kompetensi_1)) {
                $user->kompetensi_1 = 'General';
            }
        });

        static::updating(function ($user) {
            // Prevent tanggal_lahir from being set to null if it was previously set
            if ($user->isDirty('tanggal_lahir') && empty($user->tanggal_lahir)) {
                $user->tanggal_lahir = $user->getOriginal('tanggal_lahir') ?: '1990-01-01';
            }

            // Fallbacks for other NOT NULL fields if they somehow become empty
            if ($user->isDirty('agama') && empty($user->agama)) {
                $user->agama = $user->getOriginal('agama') ?: 'Lainnya';
            }
            if ($user->isDirty('pend_terakhir') && empty($user->pend_terakhir)) {
                $user->pend_terakhir = $user->getOriginal('pend_terakhir') ?: 'SMA';
            }
            if ($user->isDirty('kompetensi_1') && empty($user->kompetensi_1)) {
                $user->kompetensi_1 = $user->getOriginal('kompetensi_1') ?: 'General';
            }
        });
    }

    /**
     * Generate a unique instructor ID based on the current year.
     * Pattern: ICE[YEAR][SEQUENCE] (e.g., ICE20261)
     */
    public static function generateInstructorId()
    {
        $year = date('Y');
        $prefix = 'ICE' . $year;

        $latestUser = static::where('instructor_id', 'LIKE', "{$prefix}%")
                            ->orderByRaw('LENGTH(instructor_id) DESC')
                            ->orderBy('instructor_id', 'desc')
                            ->first();

        if ($latestUser) {
            $sequence = intval(substr($latestUser->instructor_id, 7)) + 1;
        } else {
            $sequence = 1;
        }

        return $prefix . $sequence;
    }

    protected $fillable = [
        'nama_lengkap',
        'email',
        'password',
        'tanggal_lahir',
        'no_telephone',
        'status',
        'agama',
        'pend_terakhir',
        'kompetensi_1',
        'kompetensi_2',
        'role',
        // Field untuk sistem verifikasi instruktur
        'is_verified',
        'verification_status',
        'verified_at',
        'verified_by',
        'rejection_reason',
        'verification_documents',
        'application_date',
        'division_id',
        'instructor_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'application_date' => 'datetime',
        'verification_documents' => 'array',
        'tanggal_lahir' => 'date',
    ];

    // Define relationships
    public function lateReportRequests()
    {
        return $this->hasMany(LateReportRequest::class);
    }

    public function getMonthlyLateReportQuotaAttribute()
    {
        $approvedThisMonth = $this->lateReportRequests()
            ->where("status", "approved")
            ->whereMonth("created_at", now()->month)
            ->whereYear("created_at", now()->year)
            ->count();

        return max(0, 3 - $approvedThisMonth);
    }

    public function laporanMengajar()
    {
        return $this->hasMany(LaporanMengajar::class, 'user_id_instruktur');
    }

    /**
     * Scope untuk filter instruktur pengajar (exclude staff internal).
     * Start from ID 48 (Luky).
     */
    public function scopeTeachingStaff($query)
    {
        return $query->where('role', 'instruktur')
                     ->whereIn('status', ['active', 'Aktif']);
                     // Removed ID filter to include legacy/admin instructors like ID 2
    }

    public function hasRole($roles)
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }

        return $this->role === $roles;
    }

    /**
     * Cek apakah user adalah webmaster (akses tertinggi)
     */
    public function isWebmaster(): bool
    {
        return $this->role === 'webmaster';
    }

    /**
     * Cek apakah user bisa mengelola user lain (khusus webmaster)
     */
    public function canManageUsers(): bool
    {
        return in_array($this->role, ['webmaster', 'admin_sistem']);
    }

    /**
     * Cek apakah user bisa mengakses fitur admin umum
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    /**
     * Cek apakah user adalah instruktur yang sudah terverifikasi
     */
    public function isVerifiedInstructor(): bool
    {
        return $this->role === 'instruktur' && 
               $this->is_verified && 
               $this->verification_status === 'approved';
    }

    /**
     * Relasi dengan user yang memverifikasi (webmaster)
     */
    public function verifiedBy()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Relasi dengan instruktur yang diverifikasi user ini (khusus webmaster)
     */
    public function verifiedInstructors()
    {
        return $this->hasMany(User::class, 'verified_by');
    }

    /**
     * Get the division that the user belongs to.
     */
    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    /**
     * Get the instructor profile associated with the user.
     */
    public function instructorProfile()
    {
        return $this->hasOne(InstructorProfile::class);
    }

    /**
     * Get the extracurricular sessions assigned to the instructor.
     */
    public function ekstrakurikulerSessions()
    {
        return $this->hasMany(EkstrakurikulerSession::class, 'user_id_instruktur');
    }

    /**
     * Route notifications for the WhatsApp channel.
     *
     * @return string
     */
    public function routeNotificationForWhatsapp($notification)
    {
        return $this->no_telephone;
    }

    public function payrollItems()
    {
        return $this->hasMany(PayrollItem::class, 'user_id_instruktur');
    }

    public function getLevelAttribute(): string
    {
        return $this->instructorProfile ? ($this->instructorProfile->level ?? 'junior') : 'junior';
    }
}

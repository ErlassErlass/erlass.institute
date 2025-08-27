<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory; // ADD THIS
use Illuminate\Notifications\Notifiable;
use App\Models\LaporanMengajar; // ADD THIS LINE

class User extends Authenticatable {
       use HasFactory; // ADD THIS LINE
    use Notifiable;

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
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime',
        'application_date' => 'datetime',
        'verification_documents' => 'array',
    ];

    // Define relationships
    public function laporanMengajar() {
        return $this->hasMany(LaporanMengajar::class, 'user_id_instruktur');
    }

    public function hasRole($roles) {
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
     * Cek apakah user adalah admin erlass
     */
    public function isAdminErlass(): bool
    {
        return $this->role === 'admin_erlass';
    }

    /**
     * Cek apakah user adalah instruktur yang terverifikasi
     */
    public function isVerifiedInstructor(): bool
    {
        return $this->role === 'instruktur' && $this->is_verified && $this->verification_status === 'approved';
    }

    /**
     * Cek apakah user bisa mengelola user lain (khusus webmaster)
     */
    public function canManageUsers(): bool
    {
        return $this->role === 'webmaster';
    }

    /**
     * Cek apakah user bisa mengakses fitur admin umum
     */
    public function hasAdminAccess(): bool
    {
        return in_array($this->role, ['webmaster', 'admin_erlass']);
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
}

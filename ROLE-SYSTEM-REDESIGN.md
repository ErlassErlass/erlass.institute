# Redesign Sistem Role - Dokumentasi Implementasi

## Overview

Sistem role telah didesain ulang untuk memberikan kontrol akses yang lebih granular dan aman, dengan tambahan sistem verifikasi untuk instruktur. Redesign ini mencakup perubahan struktur database, policy, middleware, dan interface pengguna.

## Struktur Role Baru

### 1. **Webmaster** (Role Tertinggi)
- **Akses**: Penuh ke semua fitur sistem
- **Kemampuan Khusus**: 
  - Mengelola semua pengguna (CRUD)
  - Verifikasi instruktur (approve/reject)
  - Akses ke semua data dan laporan
- **Batasan**: Tidak bisa menghapus akun sendiri

### 2. **Admin Erlass** (Admin Terbatas)
- **Akses**: Terbatas ke fitur operasional
- **Kemampuan**: 
  - Mengelola sekolah, siswa, laporan mengajar
  - Melihat dashboard dan statistik
  - Akses ke fitur absensi dan rekap
- **Batasan**: 
  - **TIDAK BISA** mengelola pengguna (create, update, delete users)
  - **TIDAK BISA** melakukan verifikasi instruktur

### 3. **Debug User** (Development)
- **Tujuan**: Untuk keperluan development dan testing
- **Akses**: Seperti Admin Erlass
- **Catatan**: Hanya tersedia di environment local/development

### 4. **Instruktur** (Perlu Verifikasi)
- **Status Verifikasi**: Wajib terverifikasi untuk mengakses sistem
- **Kemampuan setelah terverifikasi**:
  - Membuat dan mengelola laporan mengajar
  - Input absensi siswa
  - Melihat data sekolah dan siswa
- **Batasan**: Hanya bisa mengakses data yang terkait dengan mereka

## Sistem Verifikasi Instruktur

### Status Verifikasi
1. **Pending**: Menunggu verifikasi dari webmaster
2. **Approved**: Instruktur terverifikasi dan bisa mengakses sistem
3. **Rejected**: Aplikasi ditolak dengan alasan tertentu

### Proses Verifikasi
1. Instruktur mendaftar dengan status `pending`
2. Webmaster meninjau aplikasi di `/admin/verification`
3. Webmaster dapat:
   - **Approve**: Instruktur langsung bisa akses sistem
   - **Reject**: Dengan memberikan alasan penolakan

### Field Database Baru
```sql
- is_verified (boolean)
- verification_status (enum: pending, approved, rejected)
- verified_at (timestamp)
- verified_by (foreign key ke users)
- rejection_reason (text)
- verification_documents (json)
- application_date (timestamp)
```

## Perubahan Teknis

### 1. Model Updates
- `User.php`: Menambah methods helper untuk cek role dan verifikasi
- Relasi untuk verifikasi (verifiedBy, verifiedInstructors)

### 2. Policy Updates
- `AbsensiPolicy.php`: Update untuk role baru
- `LaporanMengajarPolicy.php`: Update untuk role baru  
- `UserPolicy.php`: Policy baru untuk manajemen user

### 3. Middleware Updates
- `RoleMiddleware.php`: Validasi verifikasi untuk instruktur

### 4. Controller Baru
- `UserManagementController.php`: Khusus untuk webmaster mengelola user

### 5. Service Layer
- `InstructorVerificationService.php`: Handle logic verifikasi instruktur

## Route Structure

### Admin Routes (Webmaster Only)
```php
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserManagementController::class);
    Route::get('verification', 'verificationIndex');
    Route::post('verification/{instructor}/approve', 'approveInstructor');
    Route::post('verification/{instructor}/reject', 'rejectInstructor');
});
```

## Migration Strategy

### Migration File: `2025_08_20_000000_redesign_user_roles_and_verification_system.php`

**Forward Migration:**
1. Menambah kolom verifikasi ke tabel users
2. Update role 'admin' menjadi 'webmaster'
3. Set semua user existing sebagai terverifikasi (backward compatibility)

**Rollback:**
- Mengembalikan role 'webmaster' ke 'admin'
- Menghapus kolom verifikasi

## UI/UX Changes

### Navigation Updates
- Menu dropdown menampilkan role user
- Webmaster: Link ke "Manajemen Pengguna" dan "Verifikasi Instruktur"
- Admin Erlass: Hanya menampilkan label role, tidak ada akses user management

### Dashboard Features
- Cards statistik verifikasi instruktur
- Role-based access untuk berbagai fitur
- Visual indicators untuk status verifikasi

### User Management Interface
- Tabel user dengan badge role dan status verifikasi
- Form create/edit user dengan role selection
- Interface verifikasi instruktur dengan approve/reject actions

## Security Improvements

### 1. Granular Access Control
- Policy-based authorization untuk setiap action
- Role-specific middleware validation
- Method-level permission checks

### 2. Instructor Verification
- Mandatory verification untuk instruktur sebelum akses sistem
- Document upload capability untuk verifikasi
- Audit trail untuk approval/rejection

### 3. Admin Separation
- Webmaster vs Admin Erlass memiliki akses yang jelas berbeda
- Admin Erlass tidak bisa "naik level" dengan membuat user baru
- Protection terhadap self-deletion

## Testing Recommendations

### 1. Unit Tests
```php
- UserTest: Test role methods (isWebmaster, isVerifiedInstructor, dll)
- InstructorVerificationServiceTest: Test verifikasi workflow
- PolicyTest: Test authorization untuk setiap role
```

### 2. Feature Tests
```php
- UserManagementTest: Test CRUD operations sesuai role
- VerificationWorkflowTest: Test approve/reject process
- AccessControlTest: Test akses halaman sesuai role
```

### 3. Manual Testing Checklist
- [ ] Webmaster bisa create/edit/delete user
- [ ] Admin Erlass tidak bisa akses user management
- [ ] Instruktur pending tidak bisa akses sistem
- [ ] Instruktur approved bisa akses fitur instruktur
- [ ] Verification workflow berjalan dengan baik
- [ ] Navigation dan UI sesuai dengan role

## Deployment Steps

1. **Backup Database** (Critical)
2. **Run Migration**: `php artisan migrate`
3. **Update Seeder**: Re-run seeder jika diperlukan
4. **Clear Cache**: `php artisan cache:clear && php artisan config:clear`
5. **Test All Roles**: Login dengan setiap role untuk memastikan akses berjalan normal

## Rollback Plan

Jika terjadi masalah:
1. **Rollback Migration**: `php artisan migrate:rollback`
2. **Restore Backup**: Restore database dari backup
3. **Check Code**: Revert ke commit sebelum perubahan jika diperlukan

---

## Migration Notes

- **Backward Compatibility**: Semua user existing akan otomatis terverifikasi
- **Role Mapping**: 
  - 'admin' → 'webmaster'
  - 'admin_erlass' tetap sama
  - 'instruktur' tetap sama tapi butuh verifikasi
- **Data Integrity**: Foreign key constraints untuk verified_by field

## Support & Troubleshooting

### Common Issues
1. **403 Unauthorized**: Cek role dan status verifikasi user
2. **Route Not Found**: Pastikan route admin sudah terdaftar
3. **Policy Error**: Verify policy registration di AuthServiceProvider

### Debug Commands
```bash
php artisan route:list --name=admin  # Check admin routes
php artisan policy:show User         # Check user policies (jika available)
```
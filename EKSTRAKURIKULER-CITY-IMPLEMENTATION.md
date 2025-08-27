# Implementasi Sistem City-Based Filtering untuk Ekstrakurikuler

## Overview
Implementasi ini mengubah sistem ekstrakurikuler dari menggunakan region yang hardcoded menjadi menggunakan data kota yang diambil dinamis dari tabel sekolah.

## Perubahan Database

### Migration
- **File**: `database/migrations/2025_08_26_100000_update_ekstrakurikuler_region_to_city.php`
- **Perubahan**: Menambah kolom `city` dan migrasi data dari region ke city berdasarkan data sekolah

### Model
- **File**: `app/Models/Ekstrakurikuler.php`
- **Perubahan**: 
  - Menambah `city` ke `$fillable`
  - Menambah `nama_program` untuk kompatibilitas
  - Menambah scope `scopeByCity()`

## Perubahan Controller

### EkstrakurikulerController
- **File**: `app/Http/Controllers/EkstrakurikulerController.php`
- **Perubahan**:
  - Method `index()`: Menambah filter city dan ambil data cities dari sekolah
  - Method `create()`: Menambah cities ke view data
  - Method `validateStep()`: Update validasi untuk city (nullable)
  - Method `getStepData()`: Include city dalam data step 1
  - Method `getSekolahByCity()`: API endpoint baru untuk AJAX

## Perubahan Routes
- **File**: `routes/web.php`
- **Perubahan**: Menambah route API `api/sekolah/by-city`

## Perubahan Request Validation

### StoreEkstrakurikulerRequest
- **File**: `app/Http/Requests/StoreEkstrakurikulerRequest.php`
- **Perubahan**: Region menjadi nullable, menambah validasi city

### UpdateEkstrakurikulerRequest
- **File**: `app/Http/Requests/UpdateEkstrakurikulerRequest.php`
- **Perubahan**: Sama dengan StoreRequest

## Perubahan Views

### Step 1 Form
- **File**: `resources/views/ekstrakurikuler/steps/step1.blade.php`
- **Perubahan**: 
  - Mengganti dropdown region dengan dropdown city
  - Menambah hidden field region untuk backward compatibility
  - Update tips dan informasi

### Step 2 Form
- **File**: `resources/views/ekstrakurikuler/steps/step2.blade.php`
- **Perubahan**: 
  - Filter sekolah berdasarkan city dari step 1
  - Pesan informasi untuk user

### Index Page
- **File**: `resources/views/ekstrakurikuler/index.blade.php`
- **Perubahan**: 
  - Menambah filter city
  - Update layout filter

### Main Create Form
- **File**: `resources/views/ekstrakurikuler/create.blade.php`
- **Perubahan**:
  - Update validasi JavaScript untuk city
  - Menambah mapping city ke region
  - Handler untuk city selection

## JavaScript Module

### EkstrakurikulerCityFilter
- **File**: `resources/js/modules/ekstrakurikuler-city-filter.js`
- **Fungsi**:
  - Handle AJAX call untuk memuat sekolah berdasarkan city
  - Loading state management
  - Error handling
  - Event dispatching

## Backward Compatibility

### Strategi Kompatibilitas
1. **Field Region**: Tetap ada untuk kompatibilitas data lama
2. **Mapping City-Region**: JavaScript mapping untuk mengisi region berdasarkan city
3. **Validation**: Region menjadi nullable, tidak breaking existing data
4. **Migration**: Otomatis populate city berdasarkan data sekolah existing

## Data Flow Baru

### Create Process
1. **Step 1**: User pilih city dari dropdown dinamis
2. **AJAX**: City selection trigger mapping ke region (hidden)
3. **Session**: Data city tersimpan untuk step berikutnya
4. **Step 2**: Dropdown sekolah difilter berdasarkan city dari step 1
5. **Store**: Data tersimpan dengan city dan region

### Index Filtering
1. **City Filter**: User dapat filter berdasarkan city
2. **School Filter**: Dapat dikombinasi dengan city filter
3. **Backward**: Region filter masih tersedia

## API Endpoints

### GET /api/sekolah/by-city
- **Parameter**: `city` (string)
- **Response**: JSON dengan list sekolah
- **Format Response**:
```json
{
  "status": "success",
  "message": "Data sekolah berhasil diambil",
  "data": [
    {
      "kodlan": "string",
      "namasekolah": "string",
      "kotkab": "string",
      "kec": "string"
    }
  ]
}
```

## Testing

### Test Cases
1. **Migration**: Pastikan data existing tidak rusak
2. **Create Flow**: Test end-to-end create dengan city selection
3. **Filter**: Test filtering di index page
4. **API**: Test API endpoint untuk berbagai city
5. **Backward Compatibility**: Test dengan data lama yang masih ada

### Manual Testing Steps
1. Jalankan migration
2. Test create ekstrakurikuler baru dengan city selection
3. Test edit ekstrakurikuler existing
4. Test filter di index page
5. Test API endpoint via browser/Postman

## Deployment Checklist

- [ ] Jalankan migration: `php artisan migrate`
- [ ] Build assets: `npm run build`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Test create flow
- [ ] Test existing data compatibility
- [ ] Monitor error logs

## Future Improvements

1. **Caching**: Cache data cities untuk performance
2. **Search**: Implementasi search dalam dropdown
3. **Bulk Update**: Tool untuk update city pada data lama
4. **Regional Admin**: Permission berdasarkan city/region
5. **Statistics**: Dashboard berdasarkan city distribution
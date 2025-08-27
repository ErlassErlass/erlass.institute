-- Script untuk sinkronisasi city field pada tabel ekstrakurikuler dengan data dari tabel sekolah

-- Tambah kolom city jika belum ada
ALTER TABLE ekstrakurikuler ADD COLUMN IF NOT EXISTS city VARCHAR(255) DEFAULT NULL AFTER region;

-- Update kolom city berdasarkan data sekolah.kotkab
UPDATE ekstrakurikuler e
INNER JOIN sekolah s ON e.sekolah_kodlan = s.kodlan 
SET e.city = s.kotkab
WHERE e.city IS NULL OR e.city = '';

-- Buat index untuk performa yang lebih baik
CREATE INDEX IF NOT EXISTS idx_ekstrakurikuler_city ON ekstrakurikuler(city);

-- Tampilkan hasil sinkronisasi
SELECT 
    COUNT(*) as total_records,
    COUNT(CASE WHEN city IS NOT NULL AND city != '' THEN 1 END) as records_with_city,
    COUNT(CASE WHEN city IS NULL OR city = '' THEN 1 END) as records_without_city
FROM ekstrakurikuler;

-- Sample data untuk verifikasi
SELECT 
    e.id,
    e.kategori_program,
    e.region,
    e.city,
    s.namasekolah,
    s.kotkab as sekolah_city
FROM ekstrakurikuler e
INNER JOIN sekolah s ON e.sekolah_kodlan = s.kodlan
LIMIT 5;
# Design: Database Indexing for Analytics

## Goal
Add a composite index to the `siswa_ekstrakurikuler` table to optimize dashboard analytics queries.

## Implementation Details
- Create migration: `2026_05_18_100000_add_analytics_performance_indexes.php`
- Index name: `se_analytics_composite_idx`
- Columns: `status`, `tanggal_daftar`, `tanggal_keluar`

## Verification
- Run `php artisan migrate`
- Check indexes using `Schema::getIndexes`
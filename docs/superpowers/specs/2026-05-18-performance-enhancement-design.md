# Design Spec: Performance Enhancement & Database Documentation (erlass.institute)

**Date:** 2026-05-18
**Topic:** Performance Optimization & Relational Mapping
**Status:** Approved

## 1. Overview
This project aims to enhance the performance of the `erlass.institute` web application (located at `/var/www/webapperlass`) through a "Balanced Sprint" approach, while providing comprehensive documentation of the database schema and relations.

## 2. Performance Enhancement Plan (Balanced Sprint)

### 2.1 Infrastructure Optimizations
- **Nginx Gzip:** Update `/etc/nginx/nginx.conf` (or site-specific config) to enable compression for `application/javascript`, `application/json`, and `text/css`.
- **Browser Caching:** Implement `Cache-Control` headers for static assets in the public directory to reduce server load for returning visitors.
- **PHP OpCache:** Ensure OpCache is properly configured for the production environment.

### 2.2 Application-Level Fixes
- **Broken Assets:** Resolve the 404 error for `js/modules/ekstrakurikuler-city-filter.js`. If the file is missing, recreate it or remove the dead reference to prevent unnecessary browser errors and console noise.
- **Laravel Optimization:** Execute `php artisan optimize` to cache config, routes, and services.

### 2.3 Database Indexing
- **Target Table:** `siswa_ekstrakurikuler`
- **Missing Indexes:** Add compound indexes on `(status, tanggal_daftar, tanggal_keluar)` to optimize the `DashboardAnalyticsController::getData` query which filters by date ranges and student status.
- **Audit:** Verify existing indexes on `laporan_mengajar` and `ekstrakurikuler_session` are being utilized effectively by the main dashboard queries.

## 3. Database Relations Documentation
A dedicated document will be created at `docs/superpowers/specs/database-relations.md` featuring:
- **Core Entities:** `users`, `sekolah`, `siswa`, `ekstrakurikuler`, `ekstrakurikuler_rombel`, `ekstrakurikuler_session`, `laporan_mengajar`, `absensi`.
- **Relationships:**
    - `users` (Instructors) -> `ekstrakurikuler_session` (1:N)
    - `ekstrakurikuler` -> `ekstrakurikuler_rombel` (1:N)
    - `siswa` -> `siswa_ekstrakurikuler` (1:N)
    - `ekstrakurikuler_session` -> `laporan_mengajar` (1:1)
    - `laporan_mengajar` -> `absensi` (1:N)
- **Visual Mapping:** Mermaid.js ERD for clear visualization of foreign key constraints.

## 4. Acceptance Criteria
- [ ] Nginx serves compressed assets.
- [ ] Page load time for `/dashboard` and `/ekstrakurikuler` improves (measured via Nginx logs).
- [ ] No 404 errors for JS/CSS assets on the main pages.
- [ ] Database relations document is complete and accurate.
- [ ] Symbolic link `/root/webapperlass` is functional.

## 5. Risks & Mitigations
- **Cache Invalidation:** Ensure that changing Nginx cache headers doesn't prevent users from seeing CSS/JS updates (mitigated by Laravel Vite's versioning).
- **Database Drift:** Changes to indexes should be made via Laravel Migrations to keep the schema synchronized.

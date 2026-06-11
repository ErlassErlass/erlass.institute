# Performance Enhancement Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Improve application performance through infrastructure tuning, asset fixes, and database indexing.

**Architecture:** Balanced approach combining server-side compression (Nginx), application-level caching (Laravel), and targeted database indexing.

**Tech Stack:** Nginx, Laravel 12.x, PHP 8.2, MySQL, Vite.

---

### Task 1: Nginx Optimization (Gzip & Cache)

**Files:**
- Modify: `/etc/nginx/sites-available/webapperlass.conf` (or `/etc/nginx/nginx.conf`)

- [ ] **Step 1: Check existing Gzip settings**

Run: `grep -r "gzip" /etc/nginx/nginx.conf`

- [ ] **Step 2: Update Nginx configuration for Gzip and Browser Caching**

```nginx
# Add inside the server block for webapperlass.conf
location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|otf)$ {
    expires 30d;
    add_header Cache-Control "public, no-transform";
    access_log off;
}

# Ensure these are in nginx.conf or the server block
gzip on;
gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
```

- [ ] **Step 3: Test Nginx configuration**

Run: `nginx -t`
Expected: `syntax is ok`, `test is successful`

- [ ] **Step 4: Reload Nginx**

Run: `systemctl reload nginx`

---

### Task 2: Resolve Broken Assets

**Files:**
- Modify: `/var/www/webapperlass/resources/views/ekstrakurikuler/index.blade.php`

- [ ] **Step 1: Verify the missing file**

Run: `ls /var/www/webapperlass/public/js/modules/ekstrakurikuler-city-filter.js`
Expected: `No such file or directory`

- [ ] **Step 2: Remove or comment out the dead reference**

```php
{{-- Remove this line or comment it out if the file doesn't exist --}}
{{-- <script src="{{ asset('js/modules/ekstrakurikuler-city-filter.js') }}"></script> --}}
```

---

### Task 3: Database Indexing

**Files:**
- Create: `/var/www/webapperlass/database/migrations/2026_05_18_100000_add_analytics_performance_indexes.php`

- [ ] **Step 1: Create the migration**

Run: `php artisan make:migration add_analytics_performance_indexes`

- [ ] **Step 2: Define the indexes in the migration**

```php
public function up(): void
{
    Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
        $table->index(['status', 'tanggal_daftar', 'tanggal_keluar'], 'se_analytics_composite_idx');
    });
}

public function down(): void
{
    Schema::table('siswa_ekstrakurikuler', function (Blueprint $table) {
        $table->dropIndex('se_analytics_composite_idx');
    });
}
```

- [ ] **Step 3: Run the migration**

Run: `php artisan migrate`

---

### Task 4: Laravel Application Optimization

**Files:**
- Command Line Only

- [ ] **Step 1: Clear and cache configuration/routes**

Run: `php artisan optimize`

- [ ] **Step 2: Verify production settings in .env**

Run: `grep "APP_DEBUG" /var/www/webapperlass/.env`
Expected: `APP_DEBUG=false`

---

### Task 5: Verification

- [ ] **Step 1: Check Gzip with curl**

Run: `curl -I -H "Accept-Encoding: gzip" https://erlass.institute`
Expected: `Content-Encoding: gzip`

- [ ] **Step 2: Check for 404s in Browser Console**
Expected: No 404 for `ekstrakurikuler-city-filter.js`.

- [ ] **Step 3: Check Dashboard Query Speed**
Run: `tail -n 50 /var/www/webapperlass/storage/logs/laravel.log` (if query logging is enabled) or monitor response time in Nginx logs.

# Database Indexing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add a composite index to the `siswa_ekstrakurikuler` table to optimize dashboard analytics queries.

**Architecture:** Create a new Laravel migration that adds a composite index on `status`, `tanggal_daftar`, and `tanggal_keluar` columns.

**Tech Stack:** PHP, Laravel (Artisan, Migration)

---

### Task 1: Create and Run Migration

**Files:**
- Create: `/root/webapperlass/database/migrations/2026_05_18_100000_add_analytics_performance_indexes.php`

- [ ] **Step 1: Create the migration file**

Run:
```bash
cat <<EOF > webapperlass/database/migrations/2026_05_18_100000_add_analytics_performance_indexes.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa_ekstrakurikuler', function (Blueprint \$table) {
            \$table->index(['status', 'tanggal_daftar', 'tanggal_keluar'], 'se_analytics_composite_idx');
        });
    }

    public function down(): void
    {
        Schema::table('siswa_ekstrakurikuler', function (Blueprint \$table) {
            \$table->dropIndex('se_analytics_composite_idx');
        });
    }
};
EOF
```

- [ ] **Step 2: Run the migration**

Run: `cd webapperlass && php artisan migrate`
Expected: Migration success output.

- [ ] **Step 3: Verify the index**

Run: `cd webapperlass && php artisan tinker --execute="print_r(Schema::getIndexes('siswa_ekstrakurikuler'))"`
Expected: `se_analytics_composite_idx` present in the list.

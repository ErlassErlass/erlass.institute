# Late Report Grace System Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Set up the database and models for the Late Report Grace System.

**Architecture:** Create a `late_report_requests` table, a `LateReportRequest` model, and update `User` and `EkstrakurikulerSession` models with necessary relationships and helpers.

**Tech Stack:** Laravel (PHP, Eloquent, Migrations)

---

### Task 1: Create Migration for `late_report_requests` table

**Files:**
- Create: `database/migrations/2026_06_04_000000_create_late_report_requests_table.php`

- [ ] **Step 1: Create the migration file**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('late_report_requests', function (Blueprint ) {
            ->id();
            ->foreignId('user_id')->constrained('users')->onDelete('cascade');
            ->foreignId('session_id')->constrained('ekstrakurikuler_session')->onDelete('cascade');
            ->text('reason');
            ->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            ->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            ->text('admin_notes')->nullable();
            ->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('late_report_requests');
    }
};
```

- [ ] **Step 2: Run the migration**

Run: `php artisan migrate` in `/var/www/webapperlass`

### Task 2: Create `LateReportRequest` Model

**Files:**
- Create: `app/Models/LateReportRequest.php`

- [ ] **Step 1: Create the model file**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateReportRequest extends Model
{
    protected $fillable = ['user_id', 'session_id', 'reason', 'status', 'admin_id', 'admin_notes'];

    public function user() { return $this->belongsTo(User::class); }
    public function session() { return $this->belongsTo(EkstrakurikulerSession::class, 'session_id'); }
    public function admin() { return $this->belongsTo(User::class, 'admin_id'); }
}
```

### Task 3: Modify `User` Model

**Files:**
- Modify: `app/Models/User.php`

- [ ] **Step 1: Add relationship and quota helper**

Add the following to `app/Models/User.php`:

```php
    public function lateReportRequests()
    {
        return $this->hasMany(LateReportRequest::class);
    }

    public function getMonthlyLateReportQuotaAttribute()
    {
        $approvedCount = $this->lateReportRequests()
            ->where('status', 'approved')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return max(0, 3 - $approvedCount);
    }
```

### Task 4: Modify `EkstrakurikulerSession` Model

**Files:**
- Modify: `app/Models/EkstrakurikulerSession.php`

- [ ] **Step 1: Add relationships**

Add the following to `app/Models/EkstrakurikulerSession.php`:

```php
    public function lateReportRequests()
    {
        return $this->hasMany(LateReportRequest::class, 'session_id');
    }

    public function latestApprovedLateReportRequest()
    {
        return $this->hasOne(LateReportRequest::class, 'session_id')
            ->where('status', 'approved')
            ->latest();
    }
```

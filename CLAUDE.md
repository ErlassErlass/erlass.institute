# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel 12 web application for managing teaching reports and student attendance in educational institutions. The system handles instructors, schools, students, teaching reports (laporan mengajar), and attendance tracking.

## Key Commands

### Development
```bash
# Start development server with queue, logs, and vite
composer run dev

# Individual services
php artisan serve                    # Laravel development server
php artisan queue:listen --tries=1  # Queue worker
php artisan pail --timeout=0        # Real-time logs
npm run dev                         # Vite asset compilation
```

### Asset Management
```bash
npm run build                       # Build production assets
npm run dev                         # Watch and compile assets
```

### Database
```bash
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed    # Fresh migration with seeders
php artisan db:seed                 # Run seeders only
```

### Testing
```bash
php artisan test                    # Run PHPUnit tests
vendor/bin/phpunit                  # Direct PHPUnit execution
```

### Code Quality
```bash
vendor/bin/pint                     # Laravel Pint code formatter
```

## Architecture Overview

### Core Models and Relationships
- **User**: Represents instructors and assistants with role-based access
- **Sekolah**: Schools identified by `kodlan` (primary key), supports SD/SMP levels
- **Siswa**: Students linked to schools via `sekolah_kodlan` and grouped by `rombel` (class groups)
- **LaporanMengajar**: Teaching reports that connect instructors, schools, and classes
- **Absensi**: Attendance records linked to teaching reports and individual students

### Key Relationships
- LaporanMengajar belongs to User (instruktur, asisten) and Sekolah
- Absensi belongs to LaporanMengajar and Siswa
- Siswa belongs to Sekolah via `sekolah_kodlan`

### Authorization
- Uses Laravel Policies (`AbsensiPolicy`, `LaporanMengajarPolicy`)
- Role-based access control through User model `hasRole()` method
- Custom middleware `RoleMiddleware` for route protection

### File Storage
- Teaching activity photos stored in `storage/app/public/laporan_mengajar/`
- Student attendance photos in `storage/app/public/laporan_mengajar_absensi/`
- Automatic file cleanup on model deletion via model boot events

### Key Services
- **AttendanceService**: Calculates dropout students and attendance metrics
- **LaporanMengajarExport**: Handles PDF/Excel export functionality using DomPDF and Maatwebsite/Excel

### Frontend Stack
- Laravel Blade templates with Tailwind CSS
- Alpine.js for interactive components
- DataTables.net for data grids
- Vite for asset bundling

### Database Design Notes
- Schools use `kodlan` as string primary key (not auto-increment)
- Students are filtered by both `sekolah_kodlan` and `rombel` for class-specific operations
- Attendance uses `updateOrCreate` to prevent duplicates
- Teaching reports auto-calculate attendance counts via relationships

### Important Patterns
- Models use `$guarded = []` with explicit `$fillable` arrays
- File uploads use Laravel's Storage facade with automatic cleanup
- Database transactions for multi-step operations (especially attendance)
- Policy-based authorization checks in controllers

### Special Routes
- `/debug-login`: Development helper for quick authentication (remove in production)
- Nested resource routes for `laporan-mengajar.absensi`
- Export routes for teaching reports in multiple formats

### Testing Structure
- Feature tests in `tests/Feature/` including auth flows
- Uses PHPUnit with Laravel's testing utilities
- Factories available for all main models
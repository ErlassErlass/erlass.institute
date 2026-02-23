<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MODIFY COLUMN is MySQL-only; SQLite uses string columns so ENUM changes are n/a
        if (DB::getDriverName() === 'mysql') {
            // 1. Expand ENUM to include ALL old and new roles temporarily
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'user', 'asisten', 'debug_user', 'admin_sistem') NOT NULL");
        }

        // 2. Migrate data (works on all drivers)
        DB::table('users')->where('role', 'admin')->update(['role' => 'admin_sistem']);
        DB::table('users')->where('role', 'admin_erlass')->update(['role' => 'admin_sistem']);
        DB::table('users')->where('role', 'asisten')->update(['role' => 'instruktur']);
        DB::table('users')->where('role', 'user')->update(['role' => 'instruktur']);
        DB::table('users')->where('role', 'debug_user')->update(['role' => 'admin_sistem']);

        // 3. Restrict ENUM to final roles (MySQL only)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('webmaster', 'admin_sistem', 'instruktur') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'user', 'asisten', 'debug_user') NOT NULL");
        }
    }
};

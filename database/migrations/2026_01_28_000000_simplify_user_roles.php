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
        // 1. Expand ENUM to include ALL old and new roles temporarily
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'user', 'asisten', 'debug_user', 'admin_sistem') NOT NULL");

        // 2. Migrate data
        
        // admin -> admin_sistem (Existing 'admin' promoted to System Admin)
        DB::table('users')->where('role', 'admin')->update(['role' => 'admin_sistem']);

        // admin_erlass -> admin_sistem (Consolidated to System Admin)
        DB::table('users')->where('role', 'admin_erlass')->update(['role' => 'admin_sistem']);
        
        // asisten -> instruktur
        DB::table('users')->where('role', 'asisten')->update(['role' => 'instruktur']);
        
        // user -> instuktur
        DB::table('users')->where('role', 'user')->update(['role' => 'instruktur']);
        
        // debug_user -> admin_sistem
        DB::table('users')->where('role', 'debug_user')->update(['role' => 'admin_sistem']);

        // 3. Restrict ENUM to final roles
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('webmaster', 'admin_sistem', 'instruktur') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // It's hard to reverse perfectly as we lost the distinction, creates a loose enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'user', 'asisten', 'debug_user') NOT NULL");
    }
};

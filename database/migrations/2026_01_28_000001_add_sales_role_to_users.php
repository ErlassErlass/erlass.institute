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
        // Add 'sales' to the ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('webmaster', 'admin_sistem', 'admin', 'instruktur', 'sales') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous ENUM (Warning: 'sales' users might face issues or need data migration before this)
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('webmaster', 'admin_sistem', 'admin', 'instruktur') NOT NULL");
    }
};

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
        // Use raw SQL to modify ENUM because Doctrine DBAL has issues with ENUMs
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'user', 'asisten', 'debug_user') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass') NOT NULL");
    }
};

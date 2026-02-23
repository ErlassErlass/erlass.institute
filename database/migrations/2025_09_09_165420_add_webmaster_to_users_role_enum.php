<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, use raw SQL to modify enum
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass', 'webmaster', 'debug_user')");
        } else {
            // For SQLite and other databases, recreate the column
            // Must drop index referencing 'role' BEFORE dropping the column
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex(['role', 'is_verified']);
            });

            Schema::table('users', function (Blueprint $table) {
                $table->string('role_new')->default('instruktur');
            });
            
            // Copy existing data
            DB::table('users')->update(['role_new' => DB::raw('role')]);
            
            // Drop and recreate with new enum values
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
                $table->enum('role', ['instruktur', 'admin', 'admin_erlass', 'webmaster', 'debug_user'])->default('instruktur');
            });
            
            // Copy data back
            DB::table('users')->update(['role' => DB::raw('role_new')]);
            
            // Drop temporary column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_new');
            });

            // Re-add the composite index
            Schema::table('users', function (Blueprint $table) {
                $table->index(['role', 'is_verified']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('instruktur', 'admin', 'admin_erlass')");
        } else {
            // For SQLite, recreate column with original values
            Schema::table('users', function (Blueprint $table) {
                $table->string('role_new')->default('instruktur');
            });
            
            DB::table('users')->update(['role_new' => DB::raw('role')]);
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role');
                $table->enum('role', ['instruktur', 'admin', 'admin_erlass'])->default('instruktur');
            });
            
            DB::table('users')->update(['role' => DB::raw('role_new')]);
            
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('role_new');
            });
        }
    }
};

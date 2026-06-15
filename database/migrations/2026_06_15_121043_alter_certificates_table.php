<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            // Drop foreign key and column 'user_id'
            if (Schema::hasColumn('certificates', 'user_id')) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Exception $e) {
                    // Ignore SQLite constraint drop failures
                }
                $table->dropColumn('user_id');
            }

            // Add 'siswa_id' foreign key and column
            if (!Schema::hasColumn('certificates', 'siswa_id')) {
                $table->foreignId('siswa_id')->nullable()->after('id')->constrained('siswa')->onDelete('cascade');
            }

            // Add 'status' column
            if (!Schema::hasColumn('certificates', 'status')) {
                $table->string('status')->default('issued')->after('file_path');
            }

            // Add 'qr_code_path' column
            if (!Schema::hasColumn('certificates', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('certificates', function (Blueprint $table) {
            if (Schema::hasColumn('certificates', 'siswa_id')) {
                try {
                    $table->dropForeign(['siswa_id']);
                } catch (\Exception $e) {
                    // Ignore SQLite constraint drop failures
                }
                $table->dropColumn('siswa_id');
            }

            if (!Schema::hasColumn('certificates', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
            }

            if (Schema::hasColumn('certificates', 'status')) {
                $table->dropColumn('status');
            }

            if (Schema::hasColumn('certificates', 'qr_code_path')) {
                $table->dropColumn('qr_code_path');
            }
        });
    }
};

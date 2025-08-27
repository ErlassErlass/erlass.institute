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
        // Menambah kolom untuk sistem verifikasi instruktur
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_verified')->default(false)->after('role');
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->after('is_verified');
            $table->timestamp('verified_at')->nullable()->after('verification_status');
            $table->unsignedBigInteger('verified_by')->nullable()->after('verified_at');
            $table->text('rejection_reason')->nullable()->after('verified_by');
            $table->json('verification_documents')->nullable()->after('rejection_reason');
            $table->timestamp('application_date')->nullable()->after('verification_documents');
            
            // Foreign key untuk verified_by
            $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            
            // Index untuk performa query
            $table->index(['role', 'is_verified']);
            $table->index(['verification_status']);
        });

        // Update role yang sudah ada
        // 1. Ubah 'admin' menjadi 'webmaster'
        DB::table('users')
            ->where('role', 'admin')
            ->update([
                'role' => 'webmaster',
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'application_date' => now()
            ]);

        // 2. Update admin_erlass tetap sama tapi dengan verifikasi
        DB::table('users')
            ->where('role', 'admin_erlass')
            ->update([
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'application_date' => now()
            ]);

        // 3. Update instruktur dengan status verifikasi
        // Set semua instruktur existing sebagai terverifikasi untuk backward compatibility
        DB::table('users')
            ->where('role', 'instruktur')
            ->update([
                'is_verified' => true,
                'verification_status' => 'approved',
                'verified_at' => now(),
                'application_date' => now(),
                // Set verified_by ke webmaster pertama yang ada
                'verified_by' => DB::table('users')->where('role', 'webmaster')->first()?->id
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan role 'webmaster' ke 'admin'
        DB::table('users')
            ->where('role', 'webmaster')
            ->update(['role' => 'admin']);

        // Hapus kolom yang ditambah
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropIndex(['role', 'is_verified']);
            $table->dropIndex(['verification_status']);
            
            $table->dropColumn([
                'is_verified',
                'verification_status',
                'verified_at',
                'verified_by',
                'rejection_reason',
                'verification_documents',
                'application_date'
            ]);
        });
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration untuk membuat tabel pivot siswa_ekstrakurikuler.
     * Tabel ini mengelola enrollment siswa dalam program ekstrakurikuler.
     */
    public function up(): void
    {
        Schema::create('siswa_ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke siswa
            $table->unsignedBigInteger('siswa_id');
            $table->foreign('siswa_id')->references('id')->on('siswa')->cascadeOnDelete();
            
            // Relasi ke ekstrakurikuler program
            $table->unsignedBigInteger('ekstrakurikuler_id');
            $table->foreign('ekstrakurikuler_id')->references('id')->on('ekstrakurikuler')->cascadeOnDelete();
            
            // Relasi ke ekstrakurikuler rombel (grup kelas dalam program)
            $table->unsignedBigInteger('ekstrakurikuler_rombel_id');
            $table->foreign('ekstrakurikuler_rombel_id', 'fk_siswa_ekskul_rombel')
                  ->references('id')->on('ekstrakurikuler_rombel')->cascadeOnDelete();
            
            // Status enrollment siswa
            $table->enum('status', [
                'aktif',        // Siswa aktif mengikuti program
                'lulus',        // Siswa sudah lulus dari program
                'keluar',       // Siswa keluar dari program (dropout)
                'pindah',       // Siswa pindah ke rombel lain
                'nonaktif'      // Siswa non-aktif sementara
            ])->default('aktif');
            
            // Tanggal enrollment
            $table->date('tanggal_daftar')->default(now());
            $table->date('tanggal_keluar')->nullable();
            
            // Alasan jika keluar/pindah
            $table->text('alasan_keluar')->nullable();
            
            // Catatan khusus
            $table->text('catatan')->nullable();
            
            // Audit trail
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Unique constraint: satu siswa hanya bisa terdaftar sekali per program
            $table->unique(['siswa_id', 'ekstrakurikuler_id'], 'unique_siswa_per_program');
            
            // Index untuk performa query
            $table->index(['ekstrakurikuler_id', 'status']);
            $table->index(['ekstrakurikuler_rombel_id', 'status']);
            $table->index(['siswa_id', 'status']);
            $table->index('tanggal_daftar');
        });
    }

    /**
     * Rollback migration.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_ekstrakurikuler');
    }
};
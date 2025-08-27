<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ekstrakurikuler_rombel', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke ekstrakurikuler
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            
            // Identifikasi rombel
            $table->string('nama_rombel'); // contoh: "Rombel 1", "Kelas A", etc
            $table->integer('nomor_rombel'); // 1, 2, 3, 4, 5
            
            // Detail siswa untuk rombel ini
            $table->integer('jumlah_siswa');
            $table->string('ruangan')->nullable();
            $table->text('keterangan_ruangan')->nullable();
            
            // Jadwal spesifik untuk rombel ini
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            
            // Frekuensi dan pertemuan
            $table->integer('total_pertemuan');
            $table->enum('frekuensi', ['harian', 'mingguan', 'dua_minggu', 'bulanan'])->default('mingguan');
            $table->integer('pertemuan_selesai')->default(0);
            
            // Instruktur assignment
            $table->foreignId('user_id_instruktur')->nullable()->constrained('users');
            $table->foreignId('user_id_asisten')->nullable()->constrained('users');
            
            // Status rombel
            $table->enum('status', ['belum_mulai', 'berlangsung', 'selesai', 'dibatalkan'])->default('belum_mulai');
            $table->text('catatan')->nullable();
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Constraints untuk memastikan nomor rombel unik per ekstrakurikuler
            $table->unique(['ekstrakurikuler_id', 'nomor_rombel']);
            
            // Indexes untuk performa
            $table->index(['ekstrakurikuler_id', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index(['hari', 'jam_mulai']);
            $table->index('user_id_instruktur');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler_rombel');
    }
};
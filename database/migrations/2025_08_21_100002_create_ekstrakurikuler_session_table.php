<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ekstrakurikuler_session', function (Blueprint $table) {
            $table->id();

            // Relasi ke ekstrakurikuler dan rombel
            $table->foreignId('ekstrakurikuler_id')->constrained('ekstrakurikuler')->onDelete('cascade');
            $table->foreignId('ekstrakurikuler_rombel_id')->constrained('ekstrakurikuler_rombel')->onDelete('cascade');

            // Detail sesi
            $table->integer('nomor_pertemuan'); // 1, 2, 3, dst
            $table->date('tanggal_terjadwal');
            $table->time('jam_mulai_terjadwal');
            $table->time('jam_selesai_terjadwal');

            // Pelaksanaan aktual
            $table->date('tanggal_pelaksanaan')->nullable();
            $table->time('jam_mulai_aktual')->nullable();
            $table->time('jam_selesai_aktual')->nullable();

            // Status sesi
            $table->enum('status', [
                'terjadwal',        // Belum dilaksanakan
                'berlangsung',      // Sedang berlangsung
                'selesai',          // Sudah selesai
                'dibatalkan',       // Dibatalkan
                'ditunda',          // Ditunda ke tanggal lain
                'tidak_hadir',       // Instruktur tidak hadir
            ])->default('terjadwal');

            // Instruktur yang bertugas
            $table->foreignId('user_id_instruktur')->nullable()->constrained('users');
            $table->foreignId('user_id_asisten')->nullable()->constrained('users');

            // Materi dan kegiatan
            $table->string('topik_materi')->nullable();
            $table->text('deskripsi_kegiatan')->nullable();

            // Integrasi dengan laporan mengajar yang ada
            $table->foreignId('laporan_mengajar_id')->nullable()->constrained('laporan_mengajar');

            // Catatan dan alasan pembatalan/penundaan
            $table->text('catatan')->nullable();
            $table->text('alasan_pembatalan')->nullable();
            $table->date('tanggal_pengganti')->nullable();

            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            // Constraints untuk memastikan nomor pertemuan unik per rombel
            $table->unique(['ekstrakurikuler_rombel_id', 'nomor_pertemuan'], 'ekskul_session_rombel_nomor_unique');

            // Indexes untuk performa
            $table->index(['ekstrakurikuler_id', 'status'], 'ekskul_session_id_status_idx');
            $table->index(['ekstrakurikuler_rombel_id', 'nomor_pertemuan'], 'ekskul_session_rombel_nomor_idx');
            $table->index(['tanggal_terjadwal', 'status'], 'ekskul_session_tgl_status_idx');
            $table->index(['tanggal_pelaksanaan', 'status'], 'ekskul_session_pelaks_status_idx');
            $table->index('user_id_instruktur', 'ekskul_session_instruktur_idx');
            $table->index('laporan_mengajar_id', 'ekskul_session_laporan_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler_session');
    }
};

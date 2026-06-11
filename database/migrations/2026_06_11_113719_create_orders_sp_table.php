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
        if (!Schema::hasTable('orders_sp')) {
            Schema::create('orders_sp', function (Blueprint $table) {
                $table->id();
                $table->string('nomor_sp')->unique();
                $table->date('tanggal_sp');
                $table->string('sekolah_kodlan');
                $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah')->onDelete('cascade');
                $table->foreignId('salesman_id')->constrained('salesmen')->onDelete('cascade');
                $table->integer('jumlah_peserta_estimasi')->default(0);
                $table->enum('jenis_kegiatan', ['eskul', 'inkul'])->default('eskul');
                $table->string('lokasi_pembelajaran')->default('Sekolah');
                $table->date('tanggal_mulai_rencana');
                $table->integer('jumlah_pertemuan');
                $table->text('catatan_khusus')->nullable();
                $table->enum('status', ['draft', 'menunggu_validasi', 'disetujui', 'berjalan', 'selesai', 'batal'])->default('draft');
                $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders_sp');
    }
};

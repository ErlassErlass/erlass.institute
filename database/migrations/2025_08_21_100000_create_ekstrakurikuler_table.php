<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ekstrakurikuler', function (Blueprint $table) {
            $table->id();
            
            // Informasi dasar program
            $table->string('nama_program');
            $table->text('deskripsi')->nullable();
            
            // Sales dan admin info
            $table->foreignId('user_id_sales')->nullable()->constrained('users');
            $table->foreignId('user_id_admin')->nullable()->constrained('users');
            $table->string('region')->nullable();
            
            // Relasi ke sekolah
            $table->string('sekolah_kodlan');
            $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah');
            
            // Detail lokasi dan kontak
            $table->text('alamat_lengkap')->nullable();
            $table->string('google_maps_link')->nullable();
            $table->decimal('jarak_km', 8, 2)->nullable();
            $table->string('kepala_sekolah')->nullable();
            $table->string('penanggung_jawab')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
            
            // Fasilitas teknis
            $table->enum('koneksi_internet', ['ada', 'tidak_ada', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->text('keterangan_internet')->nullable();
            $table->enum('proyektor', ['ada', 'tidak_ada', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->text('keterangan_proyektor')->nullable();
            $table->enum('kabel_hdmi', ['ada', 'tidak_ada', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->enum('kabel_vga', ['ada', 'tidak_ada', 'tidak_diketahui'])->default('tidak_diketahui');
            $table->text('keterangan_kabel')->nullable();
            
            // Struktur kelas
            $table->integer('total_siswa');
            $table->integer('total_ruangan');
            $table->integer('total_rombel');
            
            // Jadwal umum
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('total_pertemuan');
            $table->enum('frekuensi', ['harian', 'mingguan', 'dua_minggu', 'bulanan'])->default('mingguan');
            
            // Status dan validasi
            $table->enum('status', ['draft', 'diajukan', 'disetujui', 'ditolak', 'aktif', 'selesai', 'dibatalkan'])->default('draft');
            $table->text('catatan_status')->nullable();
            $table->timestamp('tanggal_disetujui')->nullable();
            $table->foreignId('disetujui_oleh')->nullable()->constrained('users');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes untuk performa
            $table->index(['sekolah_kodlan', 'status']);
            $table->index(['tanggal_mulai', 'tanggal_selesai']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ekstrakurikuler');
    }
};
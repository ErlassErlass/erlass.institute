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
        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Personal Info
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('no_hp_2')->nullable()->comment('Kontak darurat/keluarga');
            $table->text('alamat_domisili')->nullable();
            $table->string('kota_domisili')->nullable();
            $table->string('status_pernikahan')->nullable();
            
            // Documents & Professional
            $table->string('foto_ktp')->nullable();
            $table->string('foto_npwp')->nullable();
            $table->string('cv_link')->nullable();
            $table->string('pekerjaan_terakhir')->nullable();
            $table->string('jenjang_mengajar')->nullable();
            $table->string('universitas_jurusan')->nullable();
            
            // Financial & Legal
            $table->string('nama_bank')->nullable();
            $table->string('no_rekening')->nullable();
            $table->string('no_npwp')->nullable();
            $table->string('nik')->nullable();
            
            // Health & Physical
            $table->string('tinggi_berat_badan')->nullable();
            $table->text('riwayat_penyakit')->nullable();
            $table->string('mata_minus')->nullable();
            
            // Teaching Tools & Logistics
            $table->text('alat_mengajar')->nullable();
            $table->text('catatan_alat')->nullable();
            $table->string('kendaraan')->nullable(); // e.g. Umum, Pribadi
            $table->string('jenis_kendaraan')->nullable(); // e.g. Motor Pribadi
            
            // Schedule Preferences
            $table->json('waktu_mengajar')->nullable(); // JSON to store schedule structure
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};

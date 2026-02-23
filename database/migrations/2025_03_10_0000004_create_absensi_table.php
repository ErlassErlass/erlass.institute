<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_mengajar_id')->constrained('laporan_mengajar');
            $table->foreignId('siswa_id')->constrained('siswa');
            $table->boolean('hadir')->default(false);
            $table->string('e_signature_instruktur')->nullable();
            $table->string('e_signature_pic')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('nisn')->unique();
            $table->string('sekolah_kodlan'); // Changed from foreignId to string
            $table->string('rombel');
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswa');
    }
};

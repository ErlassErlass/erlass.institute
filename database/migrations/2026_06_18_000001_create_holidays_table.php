<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->index();
            $table->string('nama');
            $table->enum('jenis', [
                'libur_nasional',
                'cuti_bersama',
                'libur_agama',
                'hari_besar',
            ])->default('libur_nasional');
            $table->boolean('is_tanggal_merah')->default(true);
            $table->year('tahun')->index();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique('tanggal');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};

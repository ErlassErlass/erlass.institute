<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_calendars', function (Blueprint $table) {
            $table->id();
            $table->string('sekolah_kodlan')->index();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('nama');
            $table->enum('jenis', [
                'libur_sekolah',
                'ujian',
                'kegiatan_sekolah',
                'lainnya',
            ])->default('libur_sekolah');
            $table->boolean('is_blocking')->default(true)
                ->comment('Jika true, sesi ekskul tidak dapat dijadwalkan pada tanggal ini');
            $table->text('catatan')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->foreign('sekolah_kodlan')
                ->references('kodlan')
                ->on('sekolah')
                ->onDelete('cascade');

            $table->index(['sekolah_kodlan', 'tanggal_mulai', 'tanggal_selesai'], 'sc_kodlan_tgl_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_calendars');
    }
};

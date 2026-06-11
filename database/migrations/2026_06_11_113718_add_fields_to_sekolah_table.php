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
        Schema::table('sekolah', function (Blueprint $table) {
            $table->text('alamat_lengkap')->nullable();
            $table->string('pic_nama')->nullable();
            $table->string('pic_kontak')->nullable();
            $table->string('pic_email')->nullable();
            $table->enum('lokasi_default', ['sekolah', 'kantor', 'online'])->default('sekolah');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sekolah', function (Blueprint $table) {
            $table->dropColumn(['alamat_lengkap', 'pic_nama', 'pic_kontak', 'pic_email', 'lokasi_default']);
        });
    }
};

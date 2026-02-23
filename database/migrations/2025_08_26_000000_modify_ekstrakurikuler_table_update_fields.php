<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Rename nama_program to kategori_program with enum values
            $table->enum('kategori_program', [
                'Coding Scratch',
                'English Course',
                'Micro:bit Learning Kit',
                'Pictoblox AI',
                'Robotik Explorer',
                'Robotik Jimu',
            ])->after('id');

            // Remove unnecessary fields
            $table->dropForeign(['user_id_admin']);
            $table->dropColumn([
                'nama_program',
                'user_id_admin',
                'email',
                'keterangan_internet',
                'catatan_status',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Restore old structure
            $table->string('nama_program')->after('id');
            $table->foreignId('user_id_admin')->nullable()->constrained('users');
            $table->string('email')->nullable();
            $table->text('keterangan_internet')->nullable();
            $table->text('catatan_status')->nullable();

            // Drop new field
            $table->dropColumn('kategori_program');
        });
    }
};

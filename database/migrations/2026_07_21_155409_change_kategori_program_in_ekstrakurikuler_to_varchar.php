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
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->string('kategori_program', 255)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            $table->enum('kategori_program', [
                'Coding Scratch',
                'English Course',
                'Micro:bit Learning Kit',
                'Pictoblox AI',
                'Robotik Explorer',
                'Robotik Jimu',
            ])->change();
        });
    }
};

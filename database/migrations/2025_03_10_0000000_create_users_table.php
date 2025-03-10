<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('tanggal_lahir');
            $table->string('no_telephone');
            $table->string('status'); // e.g., active/inactive
            $table->string('agama');
            $table->string('pend_terakhir'); // Last education
            $table->string('kompetensi_1'); // Primary competency
            $table->string('kompetensi_2')->nullable(); // Secondary competency
            $table->enum('role', ['instruktur', 'admin', 'admin_erlass']);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};

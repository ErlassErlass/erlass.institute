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
        Schema::create('session_confirmations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ekstrakurikuler_session_id')->constrained('ekstrakurikuler_session')->onDelete('cascade');
            $table->foreignId('user_id_instruktur')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'confirmed', 'absent'])->default('pending');
            $table->dateTime('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('session_confirmations');
    }
};

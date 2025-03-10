<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sekolah', function (Blueprint $table) {
            $table->string('kodlan')->primary(); // School code (Primary Key)
            $table->string('namasekolah');
            $table->string('rank')->nullable();
            $table->enum('jenjang', ['SD', 'SMP']);
            $table->string('sub_jenjang')->nullable();
            $table->enum('status', ['Swasta', 'Negeri']);
            $table->string('pd')->nullable();
            $table->string('kec'); // District
            $table->string('kotkab'); // City/Kabupaten
            $table->string('kota'); // City
            $table->string('provinsi');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('sekolah');
    }
};
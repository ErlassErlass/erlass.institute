<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->string('level')->default('junior')->after('user_id'); // junior, madya, senior, expert, master_trainer
        });
    }

    public function down(): void
    {
        Schema::table('instructor_profiles', function (Blueprint $table) {
            $table->dropColumn('level');
        });
    }
};

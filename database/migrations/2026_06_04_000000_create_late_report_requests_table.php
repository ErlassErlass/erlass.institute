<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('late_report_requests', function (Blueprint ) {
            ->id();
            ->foreignId('user_id')->constrained('users')->onDelete('cascade');
            ->foreignId('session_id')->constrained('ekstrakurikuler_session')->onDelete('cascade');
            ->text('reason');
            ->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            ->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            ->text('admin_notes')->nullable();
            ->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('late_report_requests');
    }
};

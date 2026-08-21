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
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->unsignedTinyInteger('unreported_reminder_count')->default(0)->after('reminder_h0_sent_at')->index();
            $table->timestamp('unreported_reminder_last_sent_at')->nullable()->after('unreported_reminder_count')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler_session', function (Blueprint $table) {
            $table->dropIndex(['unreported_reminder_count']);
            $table->dropIndex(['unreported_reminder_last_sent_at']);
            $table->dropColumn(['unreported_reminder_count', 'unreported_reminder_last_sent_at']);
        });
    }
};

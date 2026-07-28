<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('late_report_requests', function (Blueprint $table) {
            $table->foreignId('session_id')->nullable()->change();
            if (!Schema::hasColumn('late_report_requests', 'adhoc_date')) {
                $table->date('adhoc_date')->nullable()->after('session_id');
            }
        });
    }

    public function down()
    {
        Schema::table('late_report_requests', function (Blueprint $table) {
            if (Schema::hasColumn('late_report_requests', 'adhoc_date')) {
                $table->dropColumn('adhoc_date');
            }
            $table->foreignId('session_id')->nullable(false)->change();
        });
    }
};

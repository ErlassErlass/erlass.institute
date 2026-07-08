<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Populate salesmen table from users with role 'sales' to prevent FK constraint failures
        $salesUsers = DB::table('users')->where('role', 'sales')->get();
        foreach ($salesUsers as $user) {
            // Check if salesman already exists with this ID or kode_salesman
            $exists = DB::table('salesmen')->where('id', $user->id)->exists();
            if (!$exists) {
                DB::table('salesmen')->insert([
                    'id' => $user->id,
                    'kode_salesman' => 'SLS-' . str_pad($user->id, 3, '0', STR_PAD_LEFT),
                    'nama_salesman' => $user->nama_lengkap,
                    'user_id' => null, // Salesmen no longer have login/user accounts
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 2. Clean up any invalid user_id_sales values in ekstrakurikuler table
        $salesmenIds = DB::table('salesmen')->pluck('id')->toArray();
        DB::table('ekstrakurikuler')
            ->whereNotIn('user_id_sales', $salesmenIds)
            ->update(['user_id_sales' => null]);

        // 3. Alter the foreign key constraint in ekstrakurikuler table
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Drop old foreign key constraint
            $table->dropForeign('ekstrakurikuler_user_id_sales_foreign');
            
            // Add new foreign key constraint pointing to salesmen table
            $table->foreign('user_id_sales')
                ->references('id')
                ->on('salesmen')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ekstrakurikuler', function (Blueprint $table) {
            // Drop new foreign key constraint
            $table->dropForeign(['user_id_sales']);
            
            // Re-add old foreign key constraint pointing to users table
            $table->foreign('user_id_sales')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }
};

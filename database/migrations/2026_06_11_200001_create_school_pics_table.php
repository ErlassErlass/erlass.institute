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
        // 1. Create school_pics table
        Schema::create('school_pics', function (Blueprint $table) {
            $table->id();
            $table->string('sekolah_kodlan');
            $table->foreign('sekolah_kodlan')->references('kodlan')->on('sekolah')->onDelete('cascade');
            $table->string('nama');
            $table->string('kontak'); // WhatsApp/phone number
            $table->string('email')->nullable();
            $table->string('jabatan')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 2. Migrate existing PIC data from sekolah to school_pics
        if (Schema::hasColumn('sekolah', 'pic_nama')) {
            $schools = DB::table('sekolah')->get();
            foreach ($schools as $school) {
                if (!empty($school->pic_nama) || !empty($school->pic_kontak) || !empty($school->pic_email)) {
                    DB::table('school_pics')->insert([
                        'sekolah_kodlan' => $school->kodlan,
                        'nama' => $school->pic_nama ?? 'PIC Sekolah',
                        'kontak' => $school->pic_kontak ?? '-',
                        'email' => $school->pic_email,
                        'jabatan' => 'Koordinator',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // 3. Drop columns from sekolah
            Schema::table('sekolah', function (Blueprint $table) {
                $table->dropColumn(['pic_nama', 'pic_kontak', 'pic_email']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Add columns back to sekolah
        Schema::table('sekolah', function (Blueprint $table) {
            $table->string('pic_nama')->nullable();
            $table->string('pic_kontak')->nullable();
            $table->string('pic_email')->nullable();
        });

        // 2. Rollback data from school_pics to sekolah
        $pics = DB::table('school_pics')->get();
        foreach ($pics as $pic) {
            DB::table('sekolah')
                ->where('kodlan', $pic->sekolah_kodlan)
                ->update([
                    'pic_nama' => $pic->nama,
                    'pic_kontak' => $pic->kontak,
                    'pic_email' => $pic->email,
                ]);
        }

        // 3. Drop school_pics table
        Schema::dropIfExists('school_pics');
    }
};

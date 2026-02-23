<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class RefineEmployeeRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $systemAdmins = [
            'Ahmad Yusril',
            'Adinda Wardania',
            'Furqon Irkhamni',
            'Putro Bagus',
        ];

        DB::transaction(function () use ($systemAdmins) {
            // 1. Promote specific users to 'admin_sistem'
            foreach ($systemAdmins as $name) {
                $user = User::where('nama_lengkap', 'LIKE', "%{$name}%")->first();
                if ($user) {
                    $user->role = 'admin_sistem';
                    $user->save();
                    $this->command->info("Promoted {$user->nama_lengkap} to admin_sistem.");
                } else {
                    $this->command->warn("User not found: {$name}");
                }
            }

            // 2. Demote other admins/system admins to 'admin' (Operational Admin)
            // Exclude: Webmasters, Sales, Instructors, and the newly promoted System Admins
            // We want to target users who are 'admin' or 'admin_sistem' but NOT in the approved list.
            
            // First, get IDs of the approved system admins
            $approvedIds = User::whereIn('role', ['admin_sistem', 'webmaster'])
                ->where(function ($query) use ($systemAdmins) {
                    foreach ($systemAdmins as $name) {
                        $query->orWhere('nama_lengkap', 'LIKE', "%{$name}%");
                    }
                    // Also keep webmasters safe
                    $query->orWhere('role', 'webmaster');
                })
                ->pluck('id');

            $others = User::whereIn('role', ['admin', 'admin_sistem', 'admin_erlass']) // admin_erlass included just in case
                ->whereNotIn('id', $approvedIds)
                ->where('role', '!=', 'webmaster')
                ->get();

            foreach ($others as $user) {
                // If they are sales or instruktur, skip (though query filters them out)
                if (in_array($user->role, ['sales', 'instruktur'])) continue;

                $oldRole = $user->role;
                $user->role = 'admin'; // Operational Admin (Safe Role)
                $user->save();
                $this->command->info("Demoted {$user->nama_lengkap} from {$oldRole} to admin (Operational).");
            }
        });
    }
}

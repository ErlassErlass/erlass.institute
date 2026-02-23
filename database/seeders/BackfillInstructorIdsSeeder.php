<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class BackfillInstructorIdsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all instructors without an ID, ordered by creation date
        $instructors = User::where('role', 'instruktur')
                           ->whereNull('instructor_id')
                           ->orderBy('created_at', 'asc')
                           ->get();

        foreach ($instructors as $user) {
            $year = $user->created_at->format('Y');

            // Determine sequence for this specific year based on registration order
            // Since we are iterating by created_at asc, we can just recount for that year ideally,
            // or stick to the database check which simulates real registration flow.
            
            $prefix = 'ICE' . $year;

            $latestUser = User::where('instructor_id', 'LIKE', "{$prefix}%")
                              ->where('id', '!=', $user->id) // Exclude current if it somehow has one (unlikely given query)
                              ->orderByRaw('LENGTH(instructor_id) DESC')
                              ->orderBy('instructor_id', 'desc')
                              ->first();

            if ($latestUser) {
                $sequence = intval(substr($latestUser->instructor_id, 7)) + 1;
            } else {
                $sequence = 1;
            }

            $instructorId = $prefix . $sequence;

            $user->update(['instructor_id' => $instructorId]);
            
            $this->command->info("Assigned {$instructorId} to {$user->nama_lengkap} (Registered: {$year})");
        }
    }
}

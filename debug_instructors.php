<?php
require __DIR__.'/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "--- DEBUG START ---\n";

$allInstructors = User::where('role', 'instruktur')->get();
echo "Total Instructors (role='instruktur'): " . $allInstructors->count() . "\n";

$scopeInstructors = User::teachingStaff()->get();
echo "Instructors via scopeTeachingStaff(): " . $scopeInstructors->count() . "\n";

echo "\nListing First 10 Instructors:\n";
foreach ($allInstructors->take(10) as $user) {
    echo "ID: {$user->id} | Name: {$user->nama_lengkap} | Role: {$user->role} | Status: '{$user->status}'\n";
}

echo "--- DEBUG END ---\n";

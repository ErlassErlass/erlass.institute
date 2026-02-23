<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ShowUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:show';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show all users with their roles and verification status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::select('nama_lengkap', 'email', 'role', 'is_verified', 'verification_status')->get();

        $this->info('=== USERS LIST ===');
        $this->newLine();

        foreach ($users as $user) {
            $verified = $user->is_verified ? 'Yes' : 'No';
            $status = $user->verification_status ?? 'N/A';

            $this->line("Name: {$user->nama_lengkap}");
            $this->line("Email: {$user->email}");
            $this->line("Role: {$user->role}");
            $this->line("Verified: {$verified} ({$status})");
            $this->newLine();
        }

        $this->info('Total users: '.$users->count());
    }
}

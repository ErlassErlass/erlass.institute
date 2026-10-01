<?php

namespace App\Policies;

use App\Models\InvoiceApproval;
use App\Models\User;

class InvoiceApprovalPolicy
{
    /**
     * Webmaster dan Admin memiliki akses penuh ke seluruh fitur penagihan.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (in_array($user->role, ['webmaster', 'admin_sistem', 'admin'])) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function view(User $user, InvoiceApproval $invoice): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function create(User $user): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function approveOperasional(User $user, InvoiceApproval $invoice): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function approveAkunting(User $user, InvoiceApproval $invoice): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function downloadPdf(User $user, InvoiceApproval $invoice): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }

    public function koreksi(User $user, InvoiceApproval $invoice): bool
    {
        return in_array($user->role, ['webmaster', 'admin_sistem', 'admin']);
    }
}

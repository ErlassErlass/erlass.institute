<?php

namespace App\Providers;

use App\Models\Ekstrakurikuler;
use App\Models\LaporanMengajar;
use App\Models\User;
use App\Policies\EkstrakurikulerPolicy;
use App\Policies\LaporanMengajarPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        LaporanMengajar::class => LaporanMengajarPolicy::class,
        User::class => UserPolicy::class,
        Ekstrakurikuler::class => EkstrakurikulerPolicy::class,
        \App\Models\EkstrakurikulerSession::class => \App\Policies\EkstrakurikulerSessionPolicy::class,
        \App\Models\ActivityLog::class => \App\Policies\ActivityLogPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\LaporanMengajar;
use App\Models\User;
use App\Models\Ekstrakurikuler;
use App\Policies\LaporanMengajarPolicy;
use App\Policies\UserPolicy;
use App\Policies\EkstrakurikulerPolicy;

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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
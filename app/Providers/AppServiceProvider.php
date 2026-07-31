<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register the InstructorVerificationService
        $this->app->singleton(\App\Services\InstructorVerificationService::class, function ($app) {
            return new \App\Services\InstructorVerificationService;
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Set Carbon locale to Indonesian
        \Carbon\Carbon::setLocale('id');

        // Set custom pagination views
        Paginator::defaultView('custom.pagination');
        Paginator::defaultSimpleView('custom.simple-pagination');

        // Prevent N+1 queries in development
        Model::preventLazyLoading(! app()->isProduction());

        // Register AbsensiObserver for audit logging attendance changes by instructors
        \App\Models\Absensi::observe(\App\Observers\AbsensiObserver::class);

        // FORCE HTTPS for Ngrok or Production
        // This fixes broken layout/mixed content issues when accessing via https://ngrok...
        if (! $this->app->runningInConsole()) {
            if ($this->app->environment('production') || str_contains(config('app.url'), 'ngrok') || request()->header('X-Forwarded-Proto') == 'https') {
                \Illuminate\Support\Facades\URL::forceScheme('https');
            }
        }
    }
}

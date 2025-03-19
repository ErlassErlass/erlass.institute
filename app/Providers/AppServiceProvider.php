<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Http\Middleware\RoleMiddleware;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(Router $router)
    {
        // Register the 'role' middleware alias so routes using 'role' can be resolved.
        $router->aliasMiddleware('role', RoleMiddleware::class);
    }

    /**
     * Register any application services.
     */
    public function register()
    {
        //
    }
}

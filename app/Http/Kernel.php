<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Keep global middleware here (if needed)
    protected $middleware = [
        \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
    ];

    // Keep middleware groups here (if needed)
    protected $middlewareGroups = [
        'web' => [
            // Web middleware...
        ],
        'api' => [
            // API middleware...
        ],
    ];

    // Remove this section entirely:
    // protected $routeMiddleware = [
    //     'role' => \App\Http\Middleware\RoleMiddleware::class,
    // ];
}
<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function ($middleware) {
        // Remove the call to global() because the middleware manager in Laravel 11 does not support it.
        // Instead, register any global middleware in your service provider or (if available) in your HTTP Kernel.
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Exception handling configuration here.
    })
    ->create();

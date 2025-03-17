<?php

// app/Http/Kernel.php
protected $routeMiddleware = [
    // Other middlewares...
    'role' => \App\Http\Middleware\RoleMiddleware::class,
];
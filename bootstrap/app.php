<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->booted(function () {
        if (isset($_SERVER['VERCEL_URL']) || env('VIEW_COMPILED_PATH')) {
            $storagePath = env('VIEW_COMPILED_PATH', '/tmp/storage');
            if ($storagePath === '/tmp' || $storagePath === '/tmp/storage') {
                 if (!is_dir($storagePath . '/framework/views')) {
                    @mkdir($storagePath . '/framework/views', 0755, true);
                    @mkdir($storagePath . '/framework/cache', 0755, true);
                    @mkdir($storagePath . '/framework/sessions', 0755, true);
                }
            }
        }
    })
    ->create();

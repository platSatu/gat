<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sudah login lalu membuka /auth/login -> langsung ke dashboard admin.
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        // Bahasa pilihan pengunjung (ID/EN), lihat app/Http/Middleware/SetLocale.php
        $middleware->web(append: [\App\Http\Middleware\SetLocale::class]);

        // Hak akses menu admin (Settings → Setting Menu), dipakai di routes/web.php grup admin.
        $middleware->alias(['akses.menu' => \App\Http\Middleware\CekAksesMenu::class]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

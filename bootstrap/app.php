<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        
        // 1. DAFTARKAN SEBAGAI ALIAS (Biar cuma aktif kalau dipanggil)
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminOnly::class,
        ]);

        // 2. WEB GROUP (Yang jalan di semua halaman)
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            // HAPUS BARIS 'admin' DARI SINI !!!
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create(); 
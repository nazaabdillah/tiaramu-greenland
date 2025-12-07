<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Kalau belum login, lempar ke Login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // 2. Kalau bukan admin, lempar ke HOME (Halaman Depan)
        // Jangan lempar ke dashboard, nanti looping!
        if ($request->user()->role !== 'admin') {
            return redirect()->route('home')->with('error', 'Akses ditolak!');
        }

        return $next($request);
    }
}
<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            // Ambil semua notifikasi (limit 10), urutkan dari yang terbaru
            'notifications' => $request->user() 
                ? $request->user()->notifications()->latest()->take(10)->get() 
                : [],
            ],
            // TAMBAHKAN INI:
            'flash' => [
                'popup_type' => fn () => $request->session()->get('popup_type'),
                'booking_id' => fn () => $request->session()->get('booking_id'),
                'message' => fn () => $request->session()->get('message'),
                'sisa_hutang' => fn () => $request->session()->get('sisa_hutang'),
            ],
        ];
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    // 1. Lempar user ke Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Terima balikan dari Google
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user ini sudah pernah login pake Google?
            $user = User::where('google_id', $googleUser->getId())->first();
            
            if (!$user) {
                // ... (logika cek email) ...

                if ($user) {
                    // Update Google ID & AVATAR jika user lama login lagi
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'avatar'    => $googleUser->getAvatar() // <--- UPDATE FOTO
                    ]);
                } else {
                    // User Baru
                    $user = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'avatar' => $googleUser->getAvatar(), // <--- SIMPAN FOTO
                        'password' => null, 
                        'email_verified_at' => now() 
                    ]);
                }
            } else {
                // User lama login, update fotonya biar fresh
                $user->update(['avatar' => $googleUser->getAvatar()]);
            }
            
            // ... (Auth::login dan redirect) ...

            // Login-kan user tersebut
            Auth::login($user);

            // Redirect ke Dashboard (Sesuai role nanti)
            // Redirect BALIK ke Halaman Depan (Peta)
            // Biar user bisa langsung lanjut booking
            return redirect()->route('home')->with('success', 'Login berhasil! Silakan pilih unit.');

        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }
    }
}
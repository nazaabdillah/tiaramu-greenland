<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminKavlingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 1. ZONA PUBLIK (Bebas Akses)
|--------------------------------------------------------------------------
| Guest bisa akses ini tanpa ditendang ke login.
*/

// Halaman Utama (Peta)
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/booking/finish', [BookingController::class, 'finish'])->name('booking.finish');
Route::get('/booking/invoice/{id}', [BookingController::class, 'printInvoice'])->name('booking.invoice');
// Login Google
Route::get('/auth/google', [SocialiteController::class, 'redirect'])->name('google.login');
Route::get('/auth/google/callback', [SocialiteController::class, 'callback']);

/*
|--------------------------------------------------------------------------
| 2. ZONA MEMBER & REDIRECTOR (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- DASHBOARD (SIMPEL VERSION) ---
    // Fungsinya cuma satu: Mengecek siapa yang login.
    // Tidak ada view di sini, cuma melempar user.
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.kavling.index');
        }
        // Kalau user biasa, kembalikan ke Peta supaya bisa booking
        return redirect()->route('home');
    })->name('dashboard');


    // --- FITUR BOOKING ---
    // User biasa hanya bisa akses ini kalau sudah login
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
    
    // Pancingan Logic
    Route::get('/booking/initiate/{id}', [BookingController::class, 'initiate'])->name('booking.initiate');

    // --- PROFILE ---
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/notifications/mark-as-read/{id}', function ($id) {
    $notification = auth()->user()->notifications()->where('id', $id)->first();
    if ($notification) {
        $notification->markAsRead();
    }
    return back();
    })->name('notifications.read');
});

/*
|--------------------------------------------------------------------------
| 3. ZONA ADMIN (Wajib Login + Role Admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () { 
    // Admin Kavling
    Route::prefix('admin/kavling')->name('admin.kavling.')->group(function () {
        // ... (Route admin kamu tetap sama) ...
        Route::get('/', [AdminKavlingController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [AdminKavlingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminKavlingController::class, 'update'])->name('update');
        Route::post('/{id}/photo', [AdminKavlingController::class, 'uploadPhoto'])->name('photo.store');
        Route::delete('/photo/{id}', [AdminKavlingController::class, 'deletePhoto'])->name('photo.delete');
    });

    // Admin Transaksi
    Route::prefix('admin/transaksi')->name('admin.booking.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::get('/export-pdf', [AdminBookingController::class, 'exportPdf'])->name('export');
        Route::delete('/{id}', [AdminBookingController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [AdminBookingController::class, 'approve'])->name('approve');
    });
});

require __DIR__.'/auth.php';
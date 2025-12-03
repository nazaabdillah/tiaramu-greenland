<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminKavlingController;
use App\Http\Controllers\BookingController; // Pastikan ini ada
use App\Http\Controllers\AdminBookingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. ROUTE PUBLIK (Tamu Boleh Akses)
Route::get('/', [HomeController::class, 'index'])->name('home');

// --- PINDAHKAN KESINI (Supaya Tamu Bisa Booking) ---
Route::post('/booking', [BookingController::class, 'store'])->name('booking.store'); 

// 2. ROUTE DASHBOARD
Route::get('/dashboard', function () {
    return redirect()->route('admin.kavling.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. GROUP KHUSUS ADMIN
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin Kavling
    Route::prefix('admin/kavling')->name('admin.kavling.')->group(function () {
        Route::get('/', [AdminKavlingController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [AdminKavlingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminKavlingController::class, 'update'])->name('update');
        Route::post('/{id}/photo', [AdminKavlingController::class, 'uploadPhoto'])->name('photo.store');
        Route::delete('/photo/{id}', [AdminKavlingController::class, 'deletePhoto'])->name('photo.delete');
    });
    Route::prefix('admin/transaksi')->name('admin.booking.')->group(function () {
        Route::get('/', [AdminBookingController::class, 'index'])->name('index');
        Route::delete('/{id}', [AdminBookingController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/approve', [AdminBookingController::class, 'approve'])->name('approve');
    });
});

require __DIR__.'/auth.php';
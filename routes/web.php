<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminKavlingController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. ROUTE PUBLIK (Tamu)
Route::get('/', [HomeController::class, 'index'])->name('home');

// 2. ROUTE DASHBOARD (Kita Bajak!)
// Begitu login, langsung lempar ke tabel admin, jangan ke dashboard kosong.
Route::get('/dashboard', function () {
    return redirect()->route('admin.kavling.index');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. GROUP KHUSUS ADMIN (Harus Login)
Route::middleware('auth')->group(function () {
    
    // Fitur Profile Bawaan
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Admin Kavling
    Route::prefix('admin/kavling')->name('admin.kavling.')->group(function () {
        Route::get('/', [AdminKavlingController::class, 'index'])->name('index');
        Route::get('/{id}/edit', [AdminKavlingController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminKavlingController::class, 'update'])->name('update');
        Route::post('/{id}/photo', [AdminKavlingController::class, 'uploadPhoto'])->name('photo.store');
        Route::delete('/photo/{id}', [AdminKavlingController::class, 'deletePhoto'])->name('photo.delete');
    });
});

require __DIR__.'/auth.php';
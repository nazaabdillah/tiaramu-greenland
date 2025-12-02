<?php

namespace App\Http\Controllers;

use App\Models\Kavling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route; // <--- Tambah ini
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Ambil data kavling (Bisnis Kita)
        $kavlings = Kavling::with('galleries')->get();

        // 2. Kirim ke Frontend
        return Inertia::render('Welcome', [
            'kavlings' => $kavlings,
            
            // --- Fitur Bawaan Laravel (Biar tombol Login tetap ada) ---
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'laravelVersion' => \Illuminate\Foundation\Application::VERSION,
            'phpVersion' => PHP_VERSION,
        ]);
    }
}
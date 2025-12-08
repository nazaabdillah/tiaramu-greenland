<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // --- TAMBAHKAN INI ---

    // 1. Relasi ke User (Pembeli)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 2. Relasi ke Kavling (Produk)
    public function kavling()
    {
        return $this->belongsTo(Kavling::class, 'kavling_id');
    }
}
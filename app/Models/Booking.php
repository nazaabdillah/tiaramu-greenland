<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded = []; // Izinkan mass assignment

    // Relasi: Booking ini pesanan untuk Kavling mana?
    public function kavling()
    {
        return $this->belongsTo(Kavling::class);
    }
}
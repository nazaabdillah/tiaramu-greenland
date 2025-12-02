<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kavling extends Model
{
    use HasFactory;

    // IZIN: Membolehkan semua kolom diisi data (biar seeding lancar)
    protected $guarded = [];

    // RELASI: Satu Kavling bisa punya banyak foto Galeri
    public function galleries()
    {
        return $this->hasMany(KavlingGallery::class);
    }
}
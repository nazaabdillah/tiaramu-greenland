<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KavlingGallery extends Model
{
    use HasFactory;

    protected $guarded = [];

    // RELASI: Foto ini milik satu Kavling tertentu
    public function kavling()
    {
        return $this->belongsTo(Kavling::class);
    }
}
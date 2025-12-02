<?php

namespace Database\Seeders;

use App\Models\Kavling;
use App\Models\KavlingGallery;
use Illuminate\Database\Seeder;

class GallerySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil semua data kavling yang sudah ada
        $kavlings = Kavling::all();

        // 2. Loop setiap kavling
        foreach ($kavlings as $kavling) {
            
            // Masukkan 3 Foto Dummy untuk setiap kavling
            $dummyImages = [
                'https://images.unsplash.com/photo-1580587771525-78b9dba3b91d?w=600&q=80', // Foto Depan
                'https://images.unsplash.com/photo-1556911220-e15b29be8c8f?w=600&q=80', // Foto Dapur
                'https://images.unsplash.com/photo-1484154218962-a1c002085d2f?w=600&q=80', // Foto Ruang Tamu
            ];

            foreach ($dummyImages as $index => $url) {
                KavlingGallery::create([
                    'kavling_id' => $kavling->id,
                    'image_path' => $url, // Nanti kalau upload asli, isinya 'uploads/foto1.jpg'
                    'caption'    => 'Foto Contoh ' . ($index + 1),
                ]);
            }
        }
    }
}
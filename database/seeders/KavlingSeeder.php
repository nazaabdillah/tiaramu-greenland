<?php

namespace Database\Seeders;

use App\Models\Kavling;
use Illuminate\Database\Seeder;

class KavlingSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tipe Komersial (Blok A) - Type 47/72
        // Sesuai PDF: 7 Unit [cite: 5]
        for ($i = 1; $i <= 7; $i++) {
            Kavling::create([
                'kode_kavling'  => 'A' . $i, 
                'blok'          => 1, 
                'nomor'         => $i,
                'tipe_rumah'    => '47/72',
                'luas_tanah'    => 72,
                'luas_bangunan' => 47,
                'harga'         => 450000000, 
                'status'        => 'available',
            ]);
        }

        // 2. Tipe Subsidi (Blok B, C, D) - Type 32/72
        // Sesuai PDF: B=6 unit, C=10 unit, D=11 unit [cite: 13]
        $subsidiData = [
            'B' => 6,  
            'C' => 10, 
            'D' => 11, 
        ];

        foreach ($subsidiData as $blokChar => $totalUnit) {
            for ($i = 1; $i <= $totalUnit; $i++) {
                Kavling::create([
                    'kode_kavling'  => $blokChar . $i,
                    'blok'          => ord($blokChar) - 64, // Ubah huruf ke angka
                    'nomor'         => $i,
                    'tipe_rumah'    => '32/72',
                    'luas_tanah'    => 72,
                    'luas_bangunan' => 32,
                    'harga'         => 160000000, 
                    'status'        => 'available',
                ]);
            }
        }
    }
}
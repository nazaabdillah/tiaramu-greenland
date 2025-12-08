<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\GallerySeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = \App\Models\User::where('email', 'test@example.com')->first();

        if (!$user) {
            \App\Models\User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'role' => 'admin'
            ]);
        }
        $this->call([
        KavlingSeeder::class, // <-- Pastikan nama class-nya benar
        GallerySeeder::class,  // <-- Ini juga
        // TransactionSeeder::class, (kalau ada)
        ]);
    }
}

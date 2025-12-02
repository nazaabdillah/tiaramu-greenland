<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kavlings', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kavling')->unique(); // Kunci koneksi ke SVG (Contoh: 'A7')
            $table->integer('blok')->nullable();
            $table->integer('nomor')->nullable();
            $table->string('tipe_rumah'); // 47/72 atau 32/72
            $table->decimal('luas_tanah', 8, 2);
            $table->decimal('luas_bangunan', 8, 2);
            $table->decimal('harga', 15, 2)->default(0);
            $table->enum('status', ['available', 'booking', 'sold'])->default('available');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kavlings');
    }
};

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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // 1. Barang yang dibeli
            // Relasi ke tabel kavlings
            $table->foreignId('kavling_id')->constrained('kavlings')->onDelete('cascade');
            
            // 2. Data Pembeli (Kita simpan manual biar fleksibel/guest checkout)
            $table->string('nama_pembeli');
            $table->string('nomor_wa');
            $table->string('nik_ktp')->nullable(); // Opsional, tapi penting buat akta tanah
            
            // 3. Detail Transaksi
            $table->decimal('total_harga', 15, 2); // 450.000.000,00
            
            // 4. Status Pembayaran (PENTING)
            // Unpaid: Baru klik booking
            // Pending: Udah pilih metode bayar di Midtrans (tapi belum bayar)
            // Paid: Udah lunas
            // Expired: Telat bayar
            // Cancel: Batal
            $table->enum('status_pembayaran', ['unpaid', 'pending', 'paid', 'expired', 'cancel'])->default('unpaid');
            
            // 5. Midtrans Stuff
            $table->string('snap_token')->nullable(); // Kunci rahasia buat popup bayar
            $table->string('midtrans_booking_id')->nullable(); // ID unik order kita
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};

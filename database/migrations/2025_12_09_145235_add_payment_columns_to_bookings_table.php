<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Menyimpan nominal yang dibayar via Tripay (misal 2.000.000)
            $table->decimal('nominal_bayar', 15, 2)->default(0)->after('total_harga');
            
            // Opsional: Keterangan tambahan (misal: "Booking Fee", "DP 1", "Pelunasan")
            $table->string('jenis_pembayaran')->default('booking_fee')->after('status_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};

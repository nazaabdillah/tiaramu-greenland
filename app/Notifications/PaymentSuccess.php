<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PaymentSuccess extends Notification
{
    use Queueable;

    public $booking;
    public $sisaHutang;

    public function __construct($booking, $sisaHutang)
    {
        $this->booking = $booking;
        $this->sisaHutang = $sisaHutang;
    }

    // [WAJIB CEK] Pastikan ini isinya 'database'
    public function via(object $notifiable): array
    {
        return ['database']; 
    }

    // [WAJIB CEK] Struktur Data
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembayaran DP Berhasil!',
            'message' => 'Kavling ' . $this->booking->kavling->kode_kavling . ' aman terkendali.',
            'sisa_hutang' => $this->sisaHutang,
            'booking_id' => $this->booking->id,
            'download_url' => route('booking.invoice', $this->booking->id),
            'type' => 'success', // Tambahan buat styling
        ];
    }
}
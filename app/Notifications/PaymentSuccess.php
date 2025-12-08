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

    public function via(object $notifiable): array
    {
        return ['database']; // <--- Simpan ke Database, bukan kirim email
    }

    // Struktur data yang disimpan ke database (JSON)
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Pembayaran DP Berhasil!',
            'message' => 'Terima kasih, pembayaran DP untuk Kavling ' . $this->booking->kavling->blok . '-' . $this->booking->kavling->nomor . ' telah diterima.',
            'amount_paid' => $this->booking->total_price, // atau nominal DP
            'sisa_hutang' => $this->sisaHutang,
            'booking_id' => $this->booking->id,
            'download_url' => route('booking.invoice', $this->booking->id),
            'icon' => 'check-circle', // Buat variasi icon di frontend
            'color' => 'text-green-500'
        ];
    }
}
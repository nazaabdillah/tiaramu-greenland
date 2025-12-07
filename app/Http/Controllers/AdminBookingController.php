<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
class AdminBookingController extends Controller
{
    public function index()
    {
        // Ambil data booking terbaru, sertakan data kavlingnya
        $bookings = Booking::with('kavling')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Booking/Index', [
            'bookings' => $bookings
        ]);
    }

    // Fitur Hapus Data (Kalau ada booking iseng/spam)
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Kembalikan status kavling jadi available dulu sebelum dihapus
        $booking->kavling->update(['status' => 'available']);
        
        $booking->delete();

        return back()->with('success', 'Data booking dihapus & Kavling kembali Available.');
    }
    
    // Fitur Approve (Manual Paid) - Persiapan kalau Midtrans belum ready
    public function approve($id)
    {
        $booking = Booking::findOrFail($id);
        
        // Update status booking
        $booking->update(['status_pembayaran' => 'paid']);
        
        // Update status kavling jadi SOLD permanen
        $booking->kavling->update(['status' => 'sold']);

        return back();
    }
    public function exportPdf()
    {
        // Ambil semua data (bisa difilter status PAID saja kalau mau)
        $bookings = Booking::with('kavling')
            ->orderBy('created_at', 'desc')
            ->get();

        // Load View Blade yang tadi kita buat
        $pdf = Pdf::loadView('pdf.laporan_transaksi', [
            'bookings' => $bookings
        ]);

        // Stream (Tampilkan di browser) atau Download (Unduh langsung)
        // Kita pilih Stream biar admin bisa preview dulu
        return $pdf->stream('Laporan-Penjualan-Tiaramu.pdf');
    }
}
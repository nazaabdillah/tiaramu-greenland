<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kavling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf; // <--- Pastikan ini ada

class BookingController extends Controller
{
    // 1. STORE: Kirim ke Tripay
    public function store(Request $request)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'nomor_wa'     => 'required|string|max:20',
            'nik_ktp'      => 'nullable|string|max:20',
            'kavling_id'   => 'required|exists:kavlings,id',
        ]);

        return DB::transaction(function () use ($request) {
            $kavling = Kavling::lockForUpdate()->find($request->kavling_id);
            
            if ($kavling->status !== 'available') {
                return redirect()->back()->withErrors(['msg' => 'Unit sold out!']);
            }

            $merchantRef = 'INV-' . time() . '-' . Str::random(4);

            $booking = Booking::create([
                'user_id'           => auth()->id(), // <--- INI TAMBAHAN PENTING
                'kavling_id'        => $kavling->id,
                'nama_pembeli'      => $request->nama_pembeli,
                'nomor_wa'          => $request->nomor_wa,
                'nik_ktp'           => $request->nik_ktp,
                'total_harga'       => $kavling->harga,
                'status_pembayaran' => 'unpaid',
                'midtrans_booking_id' => $merchantRef, 
            ]);

            $apiKey       = env('TRIPAY_API_KEY');
            $privateKey   = env('TRIPAY_PRIVATE_KEY');
            $merchantCode = env('TRIPAY_MERCHANT_CODE');
            $bookingFee   = 2000000; 

            $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $bookingFee, $privateKey);

            $data = [
                'method'         => 'QRIS',
                'merchant_ref'   => $merchantRef,
                'amount'         => $bookingFee,
                'customer_name'  => $request->nama_pembeli,
                'customer_email' => auth()->user()->email, // Pakai email akun asli
                'customer_phone' => $request->nomor_wa,
                'order_items'    => [
                    [
                        'sku'      => $kavling->kode_kavling,
                        'name'     => 'Booking Fee Kavling ' . $kavling->kode_kavling,
                        'price'    => $bookingFee,
                        'quantity' => 1
                    ]
                ],
                'return_url'   => route('booking.finish'), 
                'expired_time' => (time() + (24 * 60 * 60)),
                'signature'    => $signature
            ];

            try {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                    ->post('https://tripay.co.id/api-sandbox/transaction/create', $data);

                $result = $response->json();

                if (!$response->successful() || ($result['success'] ?? false) === false) {
                    throw new \Exception($result['message'] ?? 'Gagal Tripay');
                }

                $booking->update([
                    'payment_url' => $result['data']['checkout_url'],
                    'xendit_id'   => $result['data']['reference']
                ]);
                $kavling->update(['status' => 'booking']);

                return Inertia::location($result['data']['checkout_url']);

            } catch (\Exception $e) {
                dd('Error Tripay:', $e->getMessage());
            }
        });
    }

    // 2. FINISH: Halaman Sukses
    public function finish(Request $request)
    {
        $merchantRef = $request->query('merchant_ref');
        $tripayRef   = $request->query('reference');

        $booking = Booking::with('kavling')
            ->where('midtrans_booking_id', $merchantRef)
            ->orWhere('xendit_id', $tripayRef)
            ->first();

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan.');
        }

        if ($booking->status_pembayaran !== 'paid') {
            $booking->update(['status_pembayaran' => 'paid']);
            $booking->kavling->update(['status' => 'booking']); // Tetap booking sampai lunas total
        }

        return Inertia::render('Booking/Success', [
            'booking' => $booking
        ]);
    }

    // 3. PRINT INVOICE: Cetak PDF
    public function printInvoice($id)
    {
        $booking = Booking::with('kavling')->findOrFail($id);
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'booking' => $booking
        ]);

        return $pdf->stream('Invoice-Booking-' . $booking->midtrans_booking_id . '.pdf');
    }
    // FUNGSI 4: PANCINGAN LOGIN & AUTO OPEN MODAL
    public function initiate($id)
    {
        // Cukup redirect ke home, tapi bawa data ID kavling di session
        // Nanti Vue di Home akan baca session ini
        return redirect()->route('home')->with('open_modal_id', $id);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kavling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf; 

class BookingController extends Controller
{
    // 1. STORE: Logic Booking Fee 2 Juta + Safety Session
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
            
            // A. Kunci Kavling
            $kavling = Kavling::lockForUpdate()->find($request->kavling_id);
            
            if ($kavling->status !== 'available') {
                return redirect()->back()->withErrors(['msg' => 'Unit sold out!']);
            }

            // B. Setup Variabel Transaksi
            $merchantRef = 'INV-' . time() . '-' . Str::random(4);
            $bookingFee  = 2000000; 

            // C. Simpan Data Booking
            $booking = Booking::create([
                'user_id'           => auth()->id(),
                'kavling_id'        => $kavling->id,
                'nama_pembeli'      => $request->nama_pembeli,
                'nomor_wa'          => $request->nomor_wa,
                'nik_ktp'           => $request->nik_ktp,
                'total_harga'       => $kavling->harga,
                'nominal_bayar'     => 0,
                'status_pembayaran' => 'unpaid',
                'jenis_pembayaran'  => 'booking_fee',
                'midtrans_booking_id' => $merchantRef, 
            ]);

            // D. Siapkan Signature Tripay
            $apiKey       = env('TRIPAY_API_KEY');
            $privateKey   = env('TRIPAY_PRIVATE_KEY');
            $merchantCode = env('TRIPAY_MERCHANT_CODE');
            $signature    = hash_hmac('sha256', $merchantCode . $merchantRef . $bookingFee, $privateKey);

            // E. Request ke Tripay
            $data = [
                'method'         => 'QRIS',
                'merchant_ref'   => $merchantRef,
                'amount'         => $bookingFee,
                'customer_name'  => $request->nama_pembeli,
                'customer_email' => auth()->user()->email,
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
                // F. Tembak API Tripay
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                    ->post('https://tripay.co.id/api-sandbox/transaction/create', $data);
                
                $result = $response->json();

                if (!$response->successful() || ($result['success'] ?? false) === false) {
                    throw new \Exception($result['message'] ?? 'Gagal koneksi ke Tripay');
                }

                $reference = $result['data']['reference'];

                // G. Update Database
                $booking->update([
                    'payment_url' => $result['data']['checkout_url'],
                    'reference'   => $reference 
                ]);
                
                // H. Update Status Kavling
                $kavling->update(['status' => 'booking']);

                // [JURUS JARING PENGAMAN SESSION]
                session(['tripay_reference' => $reference]);

                // I. Lempar User ke Halaman Bayar
                return Inertia::location($result['data']['checkout_url']);

            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['msg' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
            }
        });
    }

    // 2. FINISH: Menangani Callback/Redirect User setelah Bayar
    public function finish(Request $request)
    {
        // Ambil Ref dari URL atau Session
        $reference = $request->query('reference'); 
        if (!$reference) {
            $reference = session('tripay_reference');
        }

        if (!$reference) {
            return redirect()->route('home')->with('error', 'Kehilangan jejak transaksi. Cek riwayat pesanan.');
        }

        // HAPUS SESSION (Ini kunci biar gak duplikat insert notif)
        session()->forget('tripay_reference');

        $booking = Booking::with(['user', 'kavling'])->where('reference', $reference)->firstOrFail();
        
        // UPDATE STATUS BAYAR
        if ($booking->status_pembayaran === 'unpaid') {
            $booking->update([
                'status_pembayaran' => 'paid',
                'nominal_bayar'     => 2000000
            ]);
        }

        $sisaHutang = $booking->total_harga - $booking->nominal_bayar;

        // INSERT NOTIF MANUAL (FIX: Hapus logic cek duplikat yang bikin bug)
        if ($booking->user) {
            try {
                DB::table('notifications')->insert([
                    'id' => Str::uuid()->toString(),
                    'type' => 'App\Notifications\PaymentSuccess',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $booking->user->id,
                    'data' => json_encode([
                        'title' => 'Booking Fee Diterima! ✅',
                        'message' => 'Kavling ' . $booking->kavling->kode_kavling . ' resmi dibooking.',
                        'sisa_hutang' => $sisaHutang,
                        'booking_id' => $booking->id,
                        'download_url' => route('booking.invoice', $booking->id), 
                        'type' => 'success'
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                \Log::error("Gagal Insert Notif: " . $e->getMessage());
            }
        }

        // Redirect ke Home
        return redirect()->route('home')->with([
            'popup_type'  => 'success_payment',
            'booking_id'  => $booking->id,
            'sisa_hutang' => $sisaHutang,
            'message'     => 'Pembayaran Berhasil'
        ]);
    }

    // 3. PRINT INVOICE
    public function printInvoice($id)
    {
        if (!auth()->check()) return redirect()->route('login');

        $booking = Booking::with(['user', 'kavling'])
            ->where('id', $id)
            ->where('user_id', auth()->id()) 
            ->firstOrFail();
        
        $pdf = Pdf::loadView('pdf.invoice', [
            'booking' => $booking
        ]);

        return $pdf->download('Invoice-'.$booking->kavling->kode_kavling.'.pdf');
    }

    public function initiate($id)
    {
        return redirect()->route('home')->with('open_modal_id', $id);
    }
}
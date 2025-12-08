<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kavling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // <--- PENTING
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str; // <--- PENTING
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf; 

class BookingController extends Controller
{
    // ... (Method STORE biarkan sama seperti sebelumnya) ...
    public function store(Request $request)
    {
        // ... (Copy paste isi method STORE dari kode lu yang terakhir, itu udah aman) ...
        // Kalo mau gua tulis ulang store-nya bilang aja, tapi harusnya store udah aman.
        // Fokus kita di FINISH dan INVOICE.
        
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
            $bookingFee  = 2000000;

            $booking = Booking::create([
                'user_id'           => auth()->id(),
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
            $signature = hash_hmac('sha256', $merchantCode . $merchantRef . $bookingFee, $privateKey);

            $data = [
                'method'         => 'QRIS',
                'merchant_ref'   => $merchantRef,
                'amount'         => $bookingFee,
                'customer_name'  => $request->nama_pembeli,
                'customer_email' => auth()->user()->email,
                'customer_phone' => $request->nomor_wa,
                'order_items'    => [['sku'=>$kavling->kode_kavling, 'name'=>'DP '.$kavling->kode_kavling, 'price'=>$bookingFee, 'quantity'=>1]],
                'return_url'   => route('booking.finish'), 
                'expired_time' => (time() + (24 * 60 * 60)),
                'signature'    => $signature
            ];

            try {
                $response = Http::withHeaders(['Authorization' => 'Bearer ' . $apiKey])
                    ->post('https://tripay.co.id/api-sandbox/transaction/create', $data);
                $result = $response->json();

                if (!$response->successful() || ($result['success'] ?? false) === false) throw new \Exception($result['message'] ?? 'Gagal Tripay');

                $booking->update([
                    'payment_url' => $result['data']['checkout_url'],
                    'reference'   => $result['data']['reference'] 
                ]);
                $kavling->update(['status' => 'booking']);

                return Inertia::location($result['data']['checkout_url']);

            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['msg' => 'Gagal: ' . $e->getMessage()]);
            }
        });
    }

    // 2. FINISH: Halaman Sukses (VERSI PREMAN - DIRECT DB INSERT)
    public function finish(Request $request)
    {
        $reference = $request->query('reference'); 
        
        // Safety Check
        if (!$reference) return redirect()->route('home');

        $booking = Booking::with(['user', 'kavling'])->where('reference', $reference)->firstOrFail();
        
        // Update Status
        if ($booking->status_pembayaran === 'unpaid') {
            $booking->update(['status_pembayaran' => 'paid']);
        }

        $sisaHutang = $booking->kavling->harga - 2000000;

        // --- CARA PREMAN (MANUAL INSERT) YANG TERBUKTI BERHASIL ---
        // Kita pakai cara ini karena debug tadi membuktikan blok ini jalan.
        
        if ($booking->user) {
            try {
                DB::table('notifications')->insert([
                    'id' => \Illuminate\Support\Str::uuid()->toString(),
                    'type' => 'App\Notifications\PaymentSuccess',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => $booking->user->id, // ID 3 Sesuai Debug Tadi
                    'data' => json_encode([
                        'title' => 'Pembayaran DP Berhasil!',
                        'message' => 'Kavling ' . $booking->kavling->kode_kavling . ' berhasil diamankan.',
                        'sisa_hutang' => $sisaHutang,
                        'booking_id' => $booking->id,
                        // URL Download
                        'download_url' => route('booking.invoice', $booking->id), 
                        'type' => 'success'
                    ]),
                    'read_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } catch (\Exception $e) {
                // Silent fail biar user gak kena error page, tapi catat di log
                \Log::error("Gagal Insert Notif Manual: " . $e->getMessage());
            }
        }
        // ---------------------------------------------------------

        return redirect()->route('home')->with([
            'popup_type'  => 'success_payment',
            'booking_id'  => $booking->id,
            'sisa_hutang' => $sisaHutang,
            'message'     => 'Pembayaran Berhasil'
        ]);
    }
    // 3. PRINT INVOICE (SAMA SEPERTI SEBELUMNYA)
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
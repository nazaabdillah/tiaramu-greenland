<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Kavling;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Inertia\Inertia;

class BookingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nama_pembeli' => 'required|string|max:255',
            'nomor_wa'     => 'required|string|max:20',
            'nik_ktp'      => 'nullable|string|max:20',
            'kavling_id'   => 'required|exists:kavlings,id',
        ]);

        // 1. SETUP XENDIT (KUNCI DARI CSV - PASTI BENAR)
        // Hati-hati saat copy-paste, jangan sampai ada spasi tambahan!
        $secretKey = 'xnd_development_GvHYCtUJNtoA0JlOjHscfIzO9feW5VkvnDpbJ8bppEDRMPBiMVzumhluhBNVtNQ';
        
        $config = Configuration::getDefaultConfiguration()->setApiKey('Authorization', $secretKey);
        $apiInstance = new InvoiceApi(null, $config);

        return DB::transaction(function () use ($request, $apiInstance) {
            
            // ... (kode cek kavling & insert booking SAMA SEPERTI SEBELUMNYA) ...
            // ... (bagian ini tidak perlu diubah karena sudah benar) ...
            
            // UNTUK MEMPERSINGKAT, SAYA TULIS ULANG INTI-NYA SAJA:
            
            $kavling = Kavling::lockForUpdate()->find($request->kavling_id);
            
            if ($kavling->status !== 'available') {
                return redirect()->back()->withErrors(['msg' => 'Unit sudah terjual!']);
            }

            $externalId = 'TGR-' . $kavling->kode_kavling . '-' . Str::random(5);

            $booking = Booking::create([
                'kavling_id'        => $kavling->id,
                'nama_pembeli'      => $request->nama_pembeli,
                'nomor_wa'          => $request->nomor_wa,
                'nik_ktp'           => $request->nik_ktp,
                'total_harga'       => $kavling->harga,
                'status_pembayaran' => 'unpaid',
                'midtrans_booking_id' => $externalId, 
            ]);

            $createInvoiceRequest = new \Xendit\Invoice\CreateInvoiceRequest([
                'external_id'      => $externalId,
                'amount'           => $kavling->harga,
                'description'      => 'Booking Kavling ' . $kavling->kode_kavling,
                'payer_email'      => 'guest@tiaramu.com', 
                'customer' => [
                    'given_names'   => $request->nama_pembeli,
                    'mobile_number' => $request->nomor_wa,
                ],
                'success_redirect_url' => route('home'), 
                'failure_redirect_url' => route('home'),
            ]);

            try {
                $result = $apiInstance->createInvoice($createInvoiceRequest);
                
                $booking->update([
                    'payment_url' => $result['invoice_url'],
                    'xendit_id'   => $result['id']
                ]);

                $kavling->update(['status' => 'booking']);

                return Inertia::location($result['invoice_url']);

            } catch (\Exception $e) {
                // Coba dump semua object error-nya biar kita bisa bedah isinya
                // Jangan panggil getResponseBody() dulu
                dd([
                    'Pesan Error' => $e->getMessage(),
                    'Trace' => $e->getTraceAsString(),
                    'Object Lengkap' => $e
                ]);
            }
        });
    }
}
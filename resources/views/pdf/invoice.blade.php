<!DOCTYPE html>
<html>
<head>
    <title>Invoice #{{ $booking->midtrans_booking_id }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; line-height: 1.6; }
        .container { width: 100%; margin: 0 auto; }
        
        .header { border-bottom: 2px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: bold; color: #064e3b; text-transform: uppercase; }
        .sub-logo { font-size: 12px; color: #666; }
        
        .invoice-box { float: right; text-align: right; }
        .invoice-title { font-size: 30px; font-weight: bold; color: #ccc; letter-spacing: 2px; }
        .invoice-id { font-weight: bold; color: #333; }
        
        .info-table { width: 100%; margin-bottom: 30px; }
        .info-table td { padding: 5px 0; vertical-align: top; }
        .label { font-weight: bold; color: #555; width: 120px; }
        
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th { background: #f0fdf4; color: #064e3b; padding: 12px; text-align: left; border-bottom: 1px solid #10b981; }
        .items-table td { padding: 12px; border-bottom: 1px solid #eee; }
        
        .total-section { text-align: right; margin-top: 20px; }
        .total-label { font-size: 14px; font-weight: bold; color: #666; }
        .total-amount { font-size: 28px; font-weight: bold; color: #059669; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 20px; }
        
        .stamp { 
            border: 2px solid #10b981; color: #10b981; 
            display: inline-block; padding: 5px 15px; 
            font-weight: bold; text-transform: uppercase; 
            transform: rotate(-5deg); letter-spacing: 2px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <div class="header">
            <table width="100%">
                <tr>
                    <td>
                        <div class="logo">Tiaramu Greenland</div>
                        <div class="sub-logo">Premium Estate Management<br>Jl. Raya Pangandaran No. 99</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="invoice-title">INVOICE</div>
                        <div class="invoice-id">#{{ $booking->midtrans_booking_id }}</div>
                        <div>{{ $booking->created_at->format('d F Y') }}</div>
                    </td>
                </tr>
            </table>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Pembeli</td>
                <td>: <strong>{{ $booking->nama_pembeli }}</strong></td>
            </tr>
            <tr>
                <td class="label">WhatsApp</td>
                <td>: {{ $booking->nomor_wa }}</td>
            </tr>
            <tr>
                <td class="label">NIK / KTP</td>
                <td>: {{ $booking->nik_ktp ?? '-' }}</td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th width="60%">Deskripsi Unit</th>
                    <th width="20%">Tipe</th>
                    <th width="20%" style="text-align: right;">Harga</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Kavling Blok {{ $booking->kavling->kode_kavling }}</strong><br>
                        <small style="color: #666">Status: {{ strtoupper($booking->status_pembayaran) }}</small>
                    </td>
                    <td>{{ $booking->kavling->tipe_rumah }}</td>
                    <td style="text-align: right;">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <span class="total-label">TOTAL DIBAYARKAN</span><br>
            <span class="total-amount">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</span>
            <br>
            
            @if($booking->status_pembayaran == 'paid')
                <div class="stamp">LUNAS / PAID</div>
            @else
                <div class="stamp" style="border-color: #f59e0b; color: #f59e0b;">PENDING</div>
            @endif
        </div>

        <div class="footer">
            Ini adalah bukti pembayaran yang sah dan diterbitkan secara komputerisasi.<br>
            Terima kasih telah memilih Tiaramu Greenland.
        </div>
    </div>
</body>
</html>
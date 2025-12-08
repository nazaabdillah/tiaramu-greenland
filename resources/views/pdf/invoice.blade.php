<!DOCTYPE html>
<html>
<head>
    <title>Invoice - {{ $booking->midtrans_booking_id }}</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { border-bottom: 2px solid #eee; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { color: #2d3748; margin: 0; }
        .header p { color: #718096; font-size: 12px; margin: 5px 0; }
        
        .info-table { width: 100%; margin-bottom: 20px; }
        .info-table td { vertical-align: top; }
        .title { font-size: 10px; text-transform: uppercase; color: #a0aec0; letter-spacing: 1px; font-weight: bold; }
        .content { font-size: 14px; font-weight: bold; margin-bottom: 10px; display: block; }
        
        .invoice-box { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .invoice-box th { background: #f7fafc; padding: 10px; text-align: left; font-size: 12px; border-bottom: 1px solid #e2e8f0; }
        .invoice-box td { padding: 10px; border-bottom: 1px solid #edf2f7; font-size: 13px; }
        
        .total-section { width: 100%; margin-top: 20px; text-align: right; }
        .total-row { display: inline-block; width: 100%; margin-bottom: 5px; }
        .total-label { display: inline-block; width: 150px; font-size: 12px; color: #718096; }
        .total-value { display: inline-block; width: 150px; font-weight: bold; font-size: 14px; }
        
        .grand-total { font-size: 18px; color: #e53e3e; margin-top: 10px; padding-top: 10px; border-top: 2px dashed #e2e8f0; }
        
        .stamp {
            position: absolute;
            top: 200px; 
            right: 20px; 
            color: #48bb78; 
            border: 3px solid #48bb78; 
            font-size: 24px; 
            font-weight: bold; 
            padding: 10px 30px; 
            transform: rotate(-15deg); 
            opacity: 0.7;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    @php
        // LOGIKA MATEMATIKA DI VIEW (BIAR VALID)
        $dpPaid = 2000000; // DP yang sudah dibayar
        $fullPrice = $booking->kavling->harga;
        $sisaTagihan = $fullPrice - $dpPaid;
    @endphp

    <div class="header">
        <table width="100%">
            <tr>
                <td>
                    <h1>Tiaramu Greenland</h1>
                    <p>Jl. Raya Utama No. 1, Indonesia</p>
                </td>
                <td style="text-align: right;">
                    <h2 style="margin:0;">INVOICE DP</h2>
                    <p>Ref: {{ $booking->midtrans_booking_id }}</p>
                    <p>Date: {{ $booking->created_at->format('d M Y') }}</p>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <span class="title">DITAGIHKAN KEPADA:</span>
                <span class="content">{{ $booking->nama_pembeli }}</span>
                <span style="font-size: 12px;">WhatsApp: {{ $booking->nomor_wa }}</span><br>
                <span style="font-size: 12px;">NIK: {{ $booking->nik_ktp ?? '-' }}</span>
            </td>
            <td width="50%" style="text-align: right;">
                <span class="title">UNIT PROPERTI:</span>
                <span class="content" style="font-size: 18px; color: #2b6cb0;">
                    KAVLING {{ $booking->kavling->kode_kavling }}
                </span>
                <span style="font-size: 12px;">
                    Tipe {{ $booking->kavling->tipe }} | Luas {{ $booking->kavling->luas_tanah }} m²
                </span>
            </td>
        </tr>
    </table>

    <table class="invoice-box">
        <thead>
            <tr>
                <th>Deskripsi</th>
                <th style="text-align: right;">Nominal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <strong>Booking Fee / Down Payment (DP)</strong><br>
                    <span style="color: #718096; font-size: 11px;">Pembayaran awal pengikatan unit {{ $booking->kavling->kode_kavling }}</span>
                </td>
                <td style="text-align: right;">Rp {{ number_format($dpPaid, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span class="total-label">Harga Cash Unit:</span>
            <span class="total-value">Rp {{ number_format($fullPrice, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Total Dibayar (DP):</span>
            <span class="total-value" style="color: #48bb78;">- Rp {{ number_format($dpPaid, 0, ',', '.') }}</span>
        </div>
        
        <div class="grand-total">
            <span class="total-label" style="color: #e53e3e;">SISA KEWAJIBAN:</span>
            <span class="total-value" style="color: #e53e3e;">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="stamp">LUNAS DP</div>

    <div style="margin-top: 50px; font-size: 10px; color: #aaa; text-align: center;">
        <p>Dokumen ini adalah bukti pembayaran yang sah yang diterbitkan oleh sistem.</p>
        <p>Harap simpan bukti ini untuk proses pelunasan selanjutnya.</p>
    </div>

</body>
</html>
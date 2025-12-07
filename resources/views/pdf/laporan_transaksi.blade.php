<!DOCTYPE html>
<html>
<head>
    <title>Laporan Penjualan - Tiaramu Greenland</title>
    <style>
        body { font-family: sans-serif; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px double #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #064e3b; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 12px; }
        
        .meta { margin-bottom: 20px; font-size: 12px; }
        .meta table { width: 100%; }
        
        table.data { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.data th, table.data td { border: 1px solid #ccc; padding: 6px; }
        table.data th { background-color: #ecfdf5; color: #064e3b; text-align: left; }
        
        .total-row td { font-weight: bold; background-color: #f8fafc; }
        .status-paid { color: green; font-weight: bold; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 12px; }
        .ttd { height: 60px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Tiaramu Greenland</h1>
        <p>Jl. Raya Pangandaran No. 99, Jawa Barat</p>
        <p>Telp: (0265) 123456 | Email: admin@tiaramu.com</p>
    </div>

    <div class="meta">
        <strong>Laporan Penjualan Unit</strong><br>
        Tanggal Cetak: {{ now()->format('d F Y') }}
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 5%">No</th>
                <th style="width: 15%">Tanggal</th>
                <th style="width: 20%">Pembeli</th>
                <th style="width: 15%">Unit</th>
                <th style="width: 15%">No. WA</th>
                <th style="width: 15%">Status</th>
                <th style="width: 15%; text-align: right;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $grandTotal = 0; @endphp
            @foreach($bookings as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->created_at->format('d/m/Y') }}</td>
                <td>{{ $item->nama_pembeli }}</td>
                <td>
                    <strong>{{ $item->kavling->kode_kavling }}</strong><br>
                    <span style="font-size: 9px; color: #666">Tipe {{ $item->kavling->tipe_rumah }}</span>
                </td>
                <td>{{ $item->nomor_wa }}</td>
                <td>
                    @if($item->status_pembayaran == 'paid')
                        <span class="status-paid">LUNAS</span>
                    @else
                        {{ strtoupper($item->status_pembayaran) }}
                    @endif
                </td>
                <td style="text-align: right;">
                    {{ number_format($item->total_harga, 0, ',', '.') }}
                </td>
            </tr>
            @php 
                if($item->status_pembayaran == 'paid') {
                    $grandTotal += $item->total_harga; 
                }
            @endphp
            @endforeach

            <tr class="total-row">
                <td colspan="6" style="text-align: right;">TOTAL PENDAPATAN (PAID)</td>
                <td style="text-align: right;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>Pangandaran, {{ now()->format('d F Y') }}</p>
        <p>Mengetahui,</p>
        <div class="ttd"></div>
        <p><strong>Admin Keuangan</strong></p>
    </div>

</body>
</html>
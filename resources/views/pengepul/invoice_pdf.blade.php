<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }} - CoCoLink</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .invoice-title { font-size: 16px; font-weight: bold; color: #047857; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        .table-data th, .table-data td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        .table-data th { background-color: #f9fafb; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-box { margin-top: 20px; background-color: #ecfdf5; padding: 12px; border: 1px solid #a7f3d0; border-radius: 4px; text-align: right; }
    </style>
</head>
<body>

    <table style="margin-bottom: 20px; border: none;">
        <tr>
            <td style="border: none; width: 50%; vertical-align: top;">
                <h2 style="color: #047857; margin: 0; font-size: 18px;">CoCoLink Platform</h2>
                <p style="margin: 2px 0; color: #666;">Platform Sinergi Pengolahan Limbah Kelapa</p>
                <p style="margin: 2px 0; color: #666;">Koperasi Induk Central Ops</p>
            </td>
            <td style="border: none; width: 50%; vertical-align: top;" class="text-right">
                <div class="invoice-title">INVOICE PEMESANAN</div>
                <p style="margin: 3px 0;"><strong>No. Invoice:</strong> INV/{{ $order->order_number }}</p>
                <p style="margin: 3px 0;"><strong>Tanggal:</strong> {{ $order->created_at?->format('d-m-Y') ?? now()->format('d-m-Y') }}</p>
            </td>
        </tr>
    </table>

    <hr style="border: none; border-top: 2px solid #047857; margin-bottom: 20px;">

    <table style="margin-bottom: 25px; border: none;">
        <tr>
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>DITAGIHKAN KEPADA:</strong><br>
                <span class="font-bold">{{ $order->pengepul?->name ?? 'Pelanggan' }}</span><br>
                {{ $order->delivery_address ?? 'Alamat tidak dicantumkan' }}<br>
                No. Telp: {{ $order->pengepul?->phone_number ?? '-' }}
            </td>
            <td style="border: none; width: 50%; vertical-align: top;" class="text-right">
                <strong>STATUS TRANSAKSI:</strong><br>
                <span style="font-weight: bold; font-size: 13px; color: #047857;">{{ strtoupper($order->status) }}</span>
            </td>
        </tr>
    </table>

    <table class="table-data">
        <thead>
            <tr>
                <th>Item Deskripsi Limbah</th>
                <th>Volume</th>
                <th>Harga per Unit</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-bold">{{ $order->wasteType?->name ?? 'N/A' }}</td>
                <td>{{ number_format($order->quantity, 0, ',', '.') }} {{ $order->wasteType?->unit }}</td>
                <td>Rp {{ number_format($order->price_per_unit, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <span style="font-size: 13px;" class="font-bold">TOTAL TAGIHAN: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>

    <div style="margin-top: 35px;">
        <p style="margin-bottom: 3px;"><strong>Catatan Pembayaran:</strong></p>
        <p style="color: #666; font-size: 10px; margin: 0;">Pembayaran ditransfer langsung ke Rekening Resmi Koperasi Induk Central Ops melalui jaringan terverifikasi CoCoLink.</p>
    </div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }} - CoCoLink</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: justify; margin-bottom: 20px; border-bottom: 2px solid #047857; padding-bottom: 10px; }
        .invoice-title { font-size: 18px; font-weight: bold; color: #047857; text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .total-box { margin-top: 20px; background-color: #ecfdf5; padding: 10px; border-radius: 4px; text-align: right; }
    </style>
</head>
<body>

    <table style="border: none; margin-bottom: 20px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%;">
                <h2 style="color: #047857; margin: 0;">CoCoLink Platform</h2>
                <p style="margin: 2px 0; color: #666;">Platform Sinergi Pengolahan Limbah Kelapa</p>
                <p style="margin: 2px 0; color: #666;">Koperasi Induk Central Ops</p>
            </td>
            <td style="border: none; width: 50%;" class="text-right">
                <div class="invoice-title">INVOICE PEMESANAN</div>
                <p style="margin: 2px 0;"><strong>No. Invoice:</strong> INV/{{ $order->order_number }}</p>
                <p style="margin: 2px 0;"><strong>Tanggal:</strong> {{ $order->created_at->format('d-m-Y') }}</p>
            </td>
        </tr>
    </table>

    <table style="border: none; margin-bottom: 20px;">
        <tr style="border: none;">
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>DITAGIHKAN KEPADA:</strong><br>
                {{ $order->pengepul->name }}<br>
                {{ $order->delivery_address }}<br>
                No. Telp: {{ $order->pengepul->phone_number }}
            </td>
            <td style="border: none; width: 50%; vertical-align: top;">
                <strong>STATUS TRANSAKSI:</strong><br>
                <span style="font-weight: bold; color: #047857;">{{ strtoupper($order->status) }}</span>
            </td>
        </tr>
    </table>

    <table>
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
                <td class="font-bold">{{ $order->wasteType->name }}</td>
                <td>{{ $order->quantity }} {{ $order->wasteType->unit }}</td>
                <td>Rp {{ number_format($order->price_per_unit, 0, ',', '.') }}</td>
                <td class="text-right font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-box">
        <span style="font-size: 14px;" class="font-bold">TOTAL TAGIHAN: Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
    </div>

    <div style="margin-top: 40px;">
        <p><strong>Catatan Pembayaran:</strong></p>
        <p style="color: #666; font-size: 10px;">Pembayaran ditransfer langsung ke Rekening Resmi Koperasi Induk Central Ops melalui jaringan terverifikasi CoCoLink.</p>
    </div>

</body>
</html>

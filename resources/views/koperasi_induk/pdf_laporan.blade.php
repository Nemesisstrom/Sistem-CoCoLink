<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Sinergi Limbah Kelapa</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #047857; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #047857; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        th { background-color: #f3f4f6; color: #111827; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>CoCoLink - Platform Sinergi Limbah Kelapa</h2>
        <p>Laporan Rekapitulasi Pesanan & Distribution Share Koperasi Induk</p>
        <p>Tanggal Cetak: {{ date('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>No. Order</th>
                <th>Pembeli (PT/Pengepul)</th>
                <th>Jenis Limbah</th>
                <th>Volume</th>
                <th>Alokasi Pasokan Koperasi Anak</th>
                <th>Total Nominal</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $order)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td class="font-bold">{{ $order->order_number }}</td>
                <td>{{ $order->pengepul->name }}</td>
                <td>{{ $order->wasteType->name }}</td>
                <td>{{ $order->quantity }} {{ $order->wasteType->unit }}</td>
                <td>
                    @foreach($order->allocations as $alloc)
                        • {{ $alloc->koperasiAnak->name }}: {{ $alloc->allocated_quantity }} {{ $order->wasteType->unit }}<br>
                    @endforeach
                </td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td>{{ strtoupper($order->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f9fafb;">
                <td colspan="4" class="font-bold text-right">TOTAL TERKUMULASI:</td>
                <td class="font-bold">{{ $totalVolume }} Unit</td>
                <td></td>
                <td class="font-bold text-right">Rp {{ number_format($totalNominal, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Mengetahui,</p>
        <br><br><br>
        <p><strong>Pengurus Koperasi Induk CoCoLink</strong></p>
    </div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Rekapitulasi Sinergi Limbah Kelapa</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #047857; padding-bottom: 8px; }
        .header h2 { margin: 0; color: #047857; text-transform: uppercase; font-size: 16px; }
        .header p { margin: 2px 0; font-size: 9px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #ddd; padding: 5px 6px; text-align: left; vertical-align: top; word-wrap: break-word; }
        th { background-color: #f3f4f6; color: #111827; font-size: 9px; text-transform: uppercase; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 25px; text-align: right; font-size: 9px; }
        .alloc-list { margin: 0; padding-left: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h2>CoCoLink - Platform Sinergi Limbah Kelapa</h2>
        <p>Laporan Rekapitulasi Pesanan & Distribution Share Koperasi Induk</p>
        <p>Tanggal Cetak: {{ now()->format('d-m-Y H:i') }} WIB</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No</th>
                <th style="width: 12%;">No. Order</th>
                <th style="width: 16%;">Pembeli (PT/Pengepul)</th>
                <th style="width: 12%;">Jenis Limbah</th>
                <th style="width: 10%;">Volume</th>
                <th style="width: 24%;">Alokasi Pasokan Koperasi Anak</th>
                <th style="width: 12%;" class="text-right">Total Nominal</th>
                <th style="width: 10%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $index => $order)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="font-bold">{{ $order->order_number }}</td>
                <td>{{ $order->pengepul?->name ?? 'N/A' }}</td>
                <td>{{ $order->wasteType?->name ?? 'N/A' }}</td>
                <td>{{ number_format($order->quantity, 0, ',', '.') }} {{ $order->wasteType?->unit }}</td>
                <td>
                    @if($order->allocations->count() > 0)
                        <ul class="alloc-list">
                        @foreach($order->allocations as $alloc)
                            <li>{{ $alloc->koperasiAnak?->name ?? 'N/A' }}: {{ number_format($alloc->allocated_quantity, 0, ',', '.') }} {{ $order->wasteType?->unit }}</li>
                        @endforeach
                        </ul>
                    @else
                        <em style="color: #888;">Belum Dialokasikan</em>
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                <td class="text-center font-bold">{{ strtoupper($order->status) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center" style="padding: 15px;">Tidak ada data transaksi ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f9fafb;">
                <td colspan="4" class="font-bold text-right">TOTAL TERKUMULASI:</td>
                <td class="font-bold">{{ number_format($totalVolume, 0, ',', '.') }} Unit</td>
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

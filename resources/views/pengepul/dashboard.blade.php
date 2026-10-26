@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Pembeli Industri / Pengepul</h1>
            <p class="text-sm text-gray-500">Buat pesanan limbah kelapa baru, pantau proses alokasi & pengiriman, serta cetak faktur invoice.</p>
        </div>
        <button onclick="document.getElementById('modalNewOrder').classList.remove('hidden')" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
            <span>➕</span> Buat Pesanan Baru
        </button>
    </div>

    <!-- Metrik Transaksi Pengepul -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $myOrders->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-yellow-600 uppercase">Menunggu Alokasi Induk</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $myOrders->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-blue-600 uppercase">Sedang Diproses / Alokasi</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $myOrders->whereIn('status', ['allocated', 'in_transit'])->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-emerald-600 uppercase">Total Pengeluaran Selesai</p>
            <p class="text-xl font-bold text-emerald-800 mt-1">
                Rp {{ number_format($myOrders->where('status', 'completed')->sum('total_amount'), 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Tabel Riwayat Pesanan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span>🛒</span> Riwayat & Status Pesanan Limbah Kelapa
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No. Order</th>
                        <th class="p-4">Jenis Limbah</th>
                        <th class="p-4">Jumlah Volume</th>
                        <th class="p-4">Harga / Satuan</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Status Transaksi</th>
                        <th class="p-4">Aksi / Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($myOrders as $order)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-emerald-700">{{ $order->order_number }}</td>
                        <td class="p-4 font-medium text-gray-800">{{ $order->wasteType->name }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $order->quantity }} {{ $order->wasteType->unit }}</td>
                        <td class="p-4">Rp {{ number_format($order->price_per_unit, 0, ',', '.') }} / {{ $order->wasteType->unit }}</td>
                        <td class="p-4 font-bold text-emerald-800">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="p-4">
                            @php
                                $statusBadges = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'allocated' => 'bg-blue-100 text-blue-800',
                                    'in_transit' => 'bg-indigo-100 text-indigo-800',
                                    'completed' => 'bg-green-100 text-green-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $statusBadges[$order->status] ?? 'bg-gray-100' }}">
                                {{ strtoupper(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="p-4">
                            <a href="{{ route('pengepul.orders.invoice', $order->id) }}" class="inline-flex items-center gap-1 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-800 px-3 py-1.5 rounded-lg border border-gray-200 transition">
                                <span>📄</span> Cetak Invoice
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">Belum ada riwayat pesanan yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Buat Pesanan Baru -->
<div id="modalNewOrder" class="fixed inset-0 bg-black/50 hidden backdrop-blur-sm z-50 flex justify-center items-center p-4">
    <div class="bg-white w-full max-w-lg rounded-xl shadow-lg border border-gray-100 p-6 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Form Pemesanan Limbah Kelapa Baru</h3>
            <button onclick="document.getElementById('modalNewOrder').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
        </div>
        <form action="{{ route('pengepul.orders.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Jenis Limbah Kelapa</label>
                <select name="waste_type_id" required class="w-full text-sm p-2.5 border rounded-lg bg-gray-50">
                    <option value="">-- Pilih Material Limbah --</option>
                    @foreach($wasteTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} (Satuan: {{ $type->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Jumlah Volume</label>
                    <input type="number" step="0.1" min="0.1" name="quantity" required placeholder="Contoh: 50" class="w-full text-sm p-2.5 border rounded-lg">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Kesepakatan / Satuan (Rp)</label>
                    <input type="number" min="0" name="price_per_unit" required placeholder="Contoh: 3500000" class="w-full text-sm p-2.5 border rounded-lg">
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Alamat Tujuan Pengiriman (Pabrik)</label>
                <textarea name="delivery_address" rows="3" required class="w-full text-sm p-2.5 border rounded-lg" placeholder="Masukkan lokasi penyerahan barang...">{{ auth()->user()->address }}</textarea>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="document.getElementById('modalNewOrder').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg transition">Kirim Pesanan Baru</button>
            </div>
        </form>
    </div>
</div>
@endsection

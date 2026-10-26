@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <div class="flex justify-between items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Laporan Rekapitulasi Sinergi Limbah</h1>
            <p class="text-sm text-gray-500">Filter data transaksi dan unduh dokumen resmi dalam format PDF.</p>
        </div>
        <a href="{{ route('koperasi-induk.dashboard') }}" class="text-sm text-emerald-700 font-semibold hover:underline">
            &larr; Kembali ke Dashboard
        </a>
    </div>

    <!-- Form Filter Data -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <form method="GET" action="{{ route('koperasi-induk.laporan.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Status Transaksi</label>
                <select name="status" class="w-full text-sm p-2 border rounded-lg">
                    <option value="">-- Semua Status --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm p-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm p-2 border rounded-lg">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white font-semibold py-2 px-4 rounded-lg text-sm transition">
                    Filter Data
                </button>
                <a href="{{ route('koperasi-induk.laporan.pdf', request()->query()) }}" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg text-sm transition text-center">
                    Cetak PDF
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Hasil Filter -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No. Order</th>
                        <th class="p-4">Pembeli (PT)</th>
                        <th class="p-4">Limbah</th>
                        <th class="p-4">Volume Target</th>
                        <th class="p-4">Total Biaya</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr>
                        <td class="p-4 font-bold text-emerald-700">{{ $order->order_number }}</td>
                        <td class="p-4 font-medium">{{ $order->pengepul->name }}</td>
                        <td class="p-4">{{ $order->wasteType->name }}</td>
                        <td class="p-4 font-bold">{{ $order->quantity }} {{ $order->wasteType->unit }}</td>
                        <td class="p-4">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                        <td class="p-4"><span class="px-2 py-1 text-xs font-semibold bg-gray-100 rounded-full">{{ strtoupper($order->status) }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-400">Tidak ada data yang sesuai filter.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

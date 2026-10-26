@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Central Ops - Koperasi Induk</h1>
            <p class="text-sm text-gray-500">Kelola pesanan limbah kelapa, alokasikan pasokan ke Koperasi Anak, dan pantau pengiriman.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('koperasi-induk.laporan.index') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white font-semibold px-4 py-2 rounded-lg text-sm transition shadow-sm flex items-center gap-2">
                📄 Laporan & PDF
            </a>
        </div>
    </div>

    <!-- Statistik Ringkas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Pesanan</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $orders->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-yellow-600 uppercase">Menunggu Alokasi</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $orders->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-indigo-600 uppercase">Dalam Pengiriman</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $orders->where('status', 'in_transit')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-green-600 uppercase">Selesai</p>
            <p class="text-2xl font-bold text-green-700 mt-1">{{ $orders->where('status', 'completed')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Pesanan & Alokasi Pasokan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span>🌴</span> Daftar Pesanan Limbah Masuk & Alokasi Koperasi Anak
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No. Order</th>
                        <th class="p-4">Pembeli (PT / Pengepul)</th>
                        <th class="p-4">Jenis Limbah</th>
                        <th class="p-4">Volume Target</th>
                        <th class="p-4">Status Utama</th>
                        <th class="p-4">Alokasi Ke Koperasi Anak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($orders as $order)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-emerald-700">{{ $order->order_number }}</td>
                        <td class="p-4 font-medium text-gray-800">{{ $order->pengepul->name }}</td>
                        <td class="p-4">{{ $order->wasteType->name }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $order->quantity }} {{ $order->wasteType->unit }}</td>
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
                            @if($order->allocations->count() > 0)
                                <div class="space-y-1 text-xs">
                                    @foreach($order->allocations as $alloc)
                                        <div class="flex justify-between items-center bg-gray-50 p-2 rounded border border-gray-100">
                                            <span>🏛️ <strong>{{ $alloc->koperasiAnak->name }}:</strong> {{ $alloc->allocated_quantity }} {{ $order->wasteType->unit }}</span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-200 text-gray-700">{{ strtoupper($alloc->status) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Form Alokasi Multi Koperasi Anak -->
                                <form action="{{ route('koperasi-induk.orders.allocate', $order->id) }}" method="POST" class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 space-y-2">
                                    @csrf
                                    <p class="text-xs font-bold text-emerald-900">Bagi Kuota Pasokan (Total: {{ $order->quantity }} {{ $order->wasteType->unit }}):</p>
                                    @foreach($koperasiAnakList as $index => $anak)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-medium w-36 truncate">{{ $anak->name }}:</span>
                                            <input type="hidden" name="allocations[{{ $index }}][koperasi_anak_id]" value="{{ $anak->id }}">
                                            <input type="number" step="0.1" min="0" name="allocations[{{ $index }}][allocated_quantity]" placeholder="Qty ({{ $order->wasteType->unit }})" class="w-28 text-xs p-1.5 border rounded bg-white">
                                        </div>
                                    @endforeach
                                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-1.5 rounded transition">
                                        Simpan & Bagikan Order
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada data pesanan masuk.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

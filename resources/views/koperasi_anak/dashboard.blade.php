@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Pasokan - Koperasi Anak</h1>
            <p class="text-sm text-gray-500">Kelola alokasi kuota pesanan dari Koperasi Induk, konfirmasi kesiapan barang, dan atur ketersediaan stok.</p>
        </div>
        <div class="text-right">
            <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-3 py-1 rounded-full">Unit Koperasi Terhubung</span>
            <p class="text-sm font-bold text-gray-800 mt-1">{{ auth()->user()->name }}</p>
        </div>
    </div>

    <!-- Metrik Ringkas Pasokan -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Alokasi Masuk</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $allocations->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-yellow-600 uppercase">Menunggu Konfirmasi</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $allocations->where('status', 'assigned')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-blue-600 uppercase">Dalam Proses Penyiapan</p>
            <p class="text-2xl font-bold text-blue-700 mt-1">{{ $allocations->where('status', 'accepted')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-emerald-600 uppercase">Siap Dijemput Driver</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1">{{ $allocations->where('status', 'ready')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Alokasi Pasokan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span>🏭</span> Daftar Penugasan Pasokan Limbah Masuk
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No. Order Induk</th>
                        <th class="p-4">Jenis Limbah</th>
                        <th class="p-4">Target Pasokan</th>
                        <th class="p-4">Pabrik / Tujuan</th>
                        <th class="p-4">Status Kesiapan</th>
                        <th class="p-4">Aksi / Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allocations as $allocation)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-emerald-700">{{ $allocation->order->order_number }}</td>
                        <td class="p-4 font-medium text-gray-800">{{ $allocation->order->wasteType->name }}</td>
                        <td class="p-4 font-bold text-emerald-800 text-base">
                            {{ $allocation->allocated_quantity }} {{ $allocation->order->wasteType->unit }}
                        </td>
                        <td class="p-4">
                            <p class="font-semibold text-gray-800">{{ $allocation->order->pengepul->name }}</p>
                            <p class="text-xs text-gray-400">{{ $allocation->order->delivery_address }}</p>
                        </td>
                        <td class="p-4">
                            @php
                                $statusBadges = [
                                    'assigned' => 'bg-yellow-100 text-yellow-800',
                                    'accepted' => 'bg-blue-100 text-blue-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'ready' => 'bg-emerald-100 text-emerald-800',
                                    'picked_up' => 'bg-purple-100 text-purple-800',
                                    'delivered' => 'bg-gray-100 text-gray-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $statusBadges[$allocation->status] ?? 'bg-gray-100' }}">
                                {{ strtoupper(str_replace('_', ' ', $allocation->status)) }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($allocation->status === 'assigned')
                                <div class="flex gap-2">
                                    <form action="{{ route('koperasi-anak.allocations.respond', $allocation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="accepted">
                                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Terima
                                        </button>
                                    </form>
                                    <form action="{{ route('koperasi-anak.allocations.respond', $allocation->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                            @elseif($allocation->status === 'accepted')
                                <form action="{{ route('koperasi-anak.allocations.ready', $allocation->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                        Tandai Pasokan Siap Dijemput 📦
                                    </button>
                                </form>
                            @elseif($allocation->status === 'ready')
                                <span class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                                    Menunggu Driver Penjemput 🚚
                                </span>
                            @else
                                <span class="text-xs text-gray-400 italic">Selesai / Dalam Pengiriman</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada alokasi pesanan dari Koperasi Induk saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

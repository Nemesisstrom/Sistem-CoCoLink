@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Pasokan - Koperasi Anak</h1>
            <p class="text-sm text-gray-500">Kelola alokasi kuota pesanan dari Koperasi Induk, konfirmasi kesiapan barang, dan atur ketersediaan stok.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="text-right">
                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-3 py-1 rounded-full">Unit Koperasi Terhubung</span>
                <p class="text-sm font-bold text-gray-800 mt-1">{{ auth()->user()->name }}</p>
            </div>
        </div>
    </div>

    <!-- Form Input Stok Harian -->
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <h3 class="text-md font-bold text-gray-800 mb-3 flex items-center gap-2">
            <span>➕</span> Catat Pasokan Stok Masuk Harian
        </h3>
        <form action="{{ route('koperasi-anak.daily-stock.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Limbah</label>
                <select name="waste_type_id" class="w-full text-xs p-2 border rounded-lg bg-white" required>
                    <option value="">-- Pilih Jenis --</option>
                    @foreach($wasteTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }} ({{ $type->unit }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Jumlah Masuk</label>
                <input type="number" step="0.1" min="0.1" name="stock_in" placeholder="Volume" class="w-full text-xs p-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Tanggal Masuk</label>
                <input type="date" name="entry_date" value="{{ date('Y-m-d') }}" class="w-full text-xs p-2 border rounded-lg" required>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Catatan (Opsional)</label>
                <input type="text" name="notes" placeholder="Ket. Petani/Wilayah" class="w-full text-xs p-2 border rounded-lg">
            </div>
            <div>
                <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-semibold text-xs py-2 rounded-lg transition">
                    + Simpan Stok
                </button>
            </div>
        </form>
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
                        <th class="p-4">Aksi / Penugasan Driver</th>
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
                            @elseif($allocation->status === 'accepted' && !$allocation->shipment)
                                <!-- Form Tugaskan Driver -->
                                <form action="{{ route('koperasi-anak.allocations.assign-driver', $allocation->id) }}" method="POST" class="p-2.5 bg-blue-50 rounded-lg border border-blue-100 space-y-2">
                                    @csrf
                                    <p class="text-xs font-bold text-blue-900">Tugaskan Driver Logistik:</p>
                                    <select name="driver_id" class="w-full text-xs p-1.5 border rounded bg-white" required>
                                        <option value="">-- Pilih Driver --</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                        @endforeach
                                    </select>
                                    <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" class="w-full text-xs p-1.5 border rounded bg-white" required>
                                    <button type="submit" class="w-full bg-blue-700 hover:bg-blue-800 text-white font-semibold text-xs py-1.5 rounded transition">
                                        Tugaskan & Kirim Email 🚚
                                    </button>
                                </form>
                            @elseif($allocation->shipment)
                                <div class="text-xs space-y-1">
                                    <p class="font-semibold text-gray-800">🚚 Driver: {{ $allocation->shipment->driver->name ?? 'N/A' }}</p>
                                    <p class="text-gray-400">Tgl Kirim: {{ $allocation->shipment->delivery_date }}</p>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Selesai / Ditolak</span>
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

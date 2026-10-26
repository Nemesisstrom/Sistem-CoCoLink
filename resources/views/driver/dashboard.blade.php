@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Driver Logistik</h1>
            <p class="text-sm text-gray-500">Kelola rute penjemputan limbah dari Koperasi Anak ke Pabrik Tujuan dan unggah bukti pengiriman.</p>
        </div>
        <div class="text-right">
            <span class="text-xs bg-indigo-100 text-indigo-800 font-semibold px-3 py-1 rounded-full">Mitra Logistik Active</span>
            <p class="text-sm font-bold text-gray-800 mt-1">🚚 {{ auth()->user()->name }} ({{ auth()->user()->vehicle_number ?? 'No. Plat Belum Set' }})</p>
        </div>
    </div>

    <!-- Metrik Tugas Driver -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-gray-400 uppercase">Total Tugas Masuk</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $shipments->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-yellow-600 uppercase">Perlu Penjemputan</p>
            <p class="text-2xl font-bold text-yellow-700 mt-1">{{ $shipments->where('status', 'assigned')->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-indigo-600 uppercase">Sedang Diantar</p>
            <p class="text-2xl font-bold text-indigo-700 mt-1">{{ $shipments->whereIn('status', ['picking_up', 'delivering'])->count() }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-xs font-semibold text-emerald-600 uppercase">Tugas Selesai</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1">{{ $shipments->where('status', 'completed')->count() }}</p>
        </div>
    </div>

    <!-- Tabel Daftar Penugasan Pengiriman -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                <span>🚛</span> Daftar Tugas Pengiriman Barang
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No. Pengiriman</th>
                        <th class="p-4">Titik Muat (Koperasi Anak)</th>
                        <th class="p-4">Titik Bongkar (Pengepul / PT)</th>
                        <th class="p-4">Kuantitas Material</th>
                        <th class="p-4">Status Logistik</th>
                        <th class="p-4">Aksi / Unggah Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($shipments as $shipment)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-emerald-700">{{ $shipment->shipment_number }}</td>
                        <td class="p-4">
                            <p class="font-semibold text-gray-800">{{ $shipment->allocation->koperasiAnak->name }}</p>
                            <p class="text-xs text-gray-400">{{ $shipment->allocation->koperasiAnak->address }}</p>
                        </td>
                        <td class="p-4">
                            <p class="font-semibold text-gray-800">{{ $shipment->allocation->order->pengepul->name }}</p>
                            <p class="text-xs text-gray-400">{{ $shipment->allocation->order->delivery_address }}</p>
                        </td>
                        <td class="p-4 font-bold text-gray-800">
                            {{ $shipment->allocation->allocated_quantity }} {{ $shipment->allocation->order->wasteType->unit }}
                            <br><span class="text-xs font-normal text-gray-500">({{ $shipment->allocation->order->wasteType->name }})</span>
                        </td>
                        <td class="p-4">
                            @php
                                $statusBadges = [
                                    'assigned' => 'bg-yellow-100 text-yellow-800',
                                    'picking_up' => 'bg-purple-100 text-purple-800',
                                    'delivering' => 'bg-indigo-100 text-indigo-800',
                                    'completed' => 'bg-emerald-100 text-emerald-800',
                                ];
                            @endphp
                            <span class="px-2.5 py-1 text-xs rounded-full font-semibold {{ $statusBadges[$shipment->status] ?? 'bg-gray-100' }}">
                                {{ strtoupper(str_replace('_', ' ', $shipment->status)) }}
                            </span>
                        </td>
                        <td class="p-4">
                            @if($shipment->status === 'assigned')
                                <form action="{{ route('driver.shipments.updateStatus', $shipment->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="picking_up">
                                    <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                        Mulai Penjemputan 📦
                                    </button>
                                </form>
                            @elseif($shipment->status === 'picking_up')
                                <form action="{{ route('driver.shipments.updateStatus', $shipment->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="delivering">
                                    <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition">
                                        Mulai Pengantaran 🚚
                                    </button>
                                </form>
                            @elseif($shipment->status === 'delivering')
                                <button onclick="openModalPOD({{ $shipment->id }})" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-3 py-1.5 rounded-lg font-semibold transition flex items-center gap-1">
                                    <span>📸</span> Upload Proof of Delivery
                                </button>
                            @elseif($shipment->status === 'completed')
                                @if($shipment->proof_of_delivery)
                                    <a href="{{ asset('storage/' . $shipment->proof_of_delivery) }}" target="_blank" class="text-xs text-emerald-700 font-bold underline">
                                        Lihat Bukti Foto
                                    </a>
                                @else
                                    <span class="text-xs text-emerald-700 font-bold">Selesai</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada penugasan pengiriman logistik untuk Anda.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Upload Proof of Delivery -->
<div id="modalPOD" class="fixed inset-0 bg-black/50 hidden backdrop-blur-sm z-50 flex justify-center items-center p-4">
    <div class="bg-white w-full max-w-md rounded-xl shadow-lg border border-gray-100 p-6 space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-lg font-bold text-gray-800">Unggah Bukti Pengiriman (POD)</h3>
            <button onclick="closeModalPOD()" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
        </div>
        <form id="formPOD" action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Foto Serah Terima Barang / Surat Jalan</label>
                <input type="file" name="proof_of_delivery" accept="image/*" required class="w-full text-xs p-2 border rounded-lg bg-gray-50">
                <p class="text-[10px] text-gray-400 mt-1">Format gambar: JPG, PNG, JPEG. Ukuran maks 2MB.</p>
            </div>
            <div class="flex justify-end gap-2 pt-2 border-t">
                <button type="button" onclick="closeModalPOD()" class="px-4 py-2 text-xs font-semibold bg-gray-100 text-gray-700 rounded-lg">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg">Selesaikan Pengiriman</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalPOD(shipmentId) {
        const form = document.getElementById('formPOD');
        form.action = `/driver/shipments/${shipmentId}/complete`;
        document.getElementById('modalPOD').classList.remove('hidden');
    }

    function closeModalPOD() {
        document.getElementById('modalPOD').classList.add('hidden');
    }
</script>
@endsection

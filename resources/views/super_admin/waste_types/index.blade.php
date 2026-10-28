@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Jenis Limbah / Komoditas</h2>
            <p class="text-sm text-gray-500">Atur katalog limbah kelapa, harga acuan per satuan, serta simbol/ikon komoditas.</p>
        </div>
        <button data-bs-toggle="modal" data-bs-target="#addWasteTypeModal" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1">
            <span>➕</span> Tambah Jenis Limbah
        </button>
    </div>

    {{-- Tabel Data Jenis Limbah --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">Ikon</th>
                        <th class="p-4">Kode Limbah</th>
                        <th class="p-4">Nama Jenis Limbah</th>
                        <th class="p-4">Harga Acuan / Satuan</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($wasteTypes as $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 text-xl">
                            <span class="p-2 bg-emerald-50 text-emerald-600 rounded-lg border border-emerald-100 inline-block">
                                <i class="{{ $item->icon ?? 'bi-box' }}"></i>
                            </span>
                        </td>
                        <td class="p-4 font-mono text-xs font-bold text-gray-700">{{ $item->code }}</td>
                        <td class="p-4 font-bold text-gray-800">{{ $item->name }}</td>
                        <td class="p-4 font-bold text-emerald-700">
                            Rp {{ number_format($item->price_per_unit, 0, ',', '.') }} / <span class="text-xs text-gray-500 font-normal">{{ $item->unit ?? 'ton' }}</span>
                        </td>
                        <td class="p-4 text-gray-500 max-w-xs truncate">{{ $item->description ?? '-' }}</td>
                        <td class="p-4 text-right space-x-2">
                            <button data-bs-toggle="modal" data-bs-target="#editWasteTypeModal{{ $item->id }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold px-3 py-1.5 rounded-md border border-blue-200 transition">
                                Edit
                            </button>
                            <form id="delete-waste-form-{{ $item->id }}" action="{{ route('super-admin.waste-types.destroy', $item->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-waste-form-{{ $item->id }}', 'Hapus Jenis Limbah {{ $item->name }}?', 'Data komoditas ini akan dihapus dari katalog master.')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-3 py-1.5 rounded-md border border-red-200 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-400 italic">Belum ada jenis limbah yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $wasteTypes->links() }}
        </div>
    </div>
</div>

{{-- Loop Modal Edit Jenis Limbah --}}
@foreach($wasteTypes as $item)
<div class="modal fade" id="editWasteTypeModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.waste-types.update', $item->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title font-bold text-gray-800">Edit Jenis Limbah - {{ $item->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-3 text-left">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Limbah</label>
                    <input type="text" name="code" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $item->code }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Komoditas</label>
                    <input type="text" name="name" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $item->name }}" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Acuan (Rp)</label>
                        <input type="number" name="price_per_unit" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $item->price_per_unit }}" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Satuan Unit Utama</label>
                        <select name="unit" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="ton" {{ $item->unit == 'ton' ? 'selected' : '' }}>Ton (Satuan Tertinggi Bulk)</option>
                            <option value="kg" {{ $item->unit == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                            <option value="liter" {{ $item->unit == 'liter' ? 'selected' : '' }}>Liter (L)</option>
                            <option value="pcs" {{ $item->unit == 'pcs' ? 'selected' : '' }}>Pcs / Karung</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ikon Bootstrap (e.g. bi-fire, bi-box, bi-droplet-fill)</label>
                    <input type="text" name="icon" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $item->icon ?? 'bi-box' }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2">{{ $item->description }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Modal Tambah Jenis Limbah --}}
<div class="modal fade" id="addWasteTypeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.waste-types.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-bold text-gray-800">Tambah Jenis Limbah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-3 text-left">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Kode Limbah</label>
                    <input type="text" name="code" placeholder="Contoh: TON-SABUT, TON-ARNG" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Komoditas Limbah</label>
                    <input type="text" name="name" placeholder="Contoh: Sabut Kelapa Mentah" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Harga Acuan (Rp)</label>
                        <input type="number" name="price_per_unit" placeholder="3500000" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Satuan Unit Utama</label>
                        <select name="unit" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                            <option value="ton" selected>Ton (Satuan Tertinggi Bulk)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="liter">Liter (L)</option>
                            <option value="pcs">Pcs / Karung</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Ikon Class (e.g. bi-fire, bi-droplet-fill, bi-box-seam)</label>
                    <input type="text" name="icon" value="bi-box-seam" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Deskripsi</label>
                    <textarea name="description" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" rows="2" placeholder="Catatan/spesifikasi limbah..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">Simpan Komoditas</button>
            </div>
        </form>
    </div>
</div>
@endsection

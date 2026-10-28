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

    {{-- Alert Notification --}}
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

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
                            Rp {{ number_format($item->price_per_kg, 0, ',', '.') }} / <span class="text-xs text-gray-500 font-normal">{{ $item->unit ?? 'kg' }}</span>
                        </td>
                        <td class="p-4 text-gray-500 max-w-xs truncate">{{ $item->description ?? '-' }}</td>
                        <td class="p-4 text-right space-x-2">
                            <button data-bs-toggle="modal" data-bs-target="#editWasteTypeModal{{ $item->id }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold px-3 py-1.5 rounded-md border border-blue-200 transition">
                                Edit
                            </button>
                            <form action="{{ route('super-admin.waste-types.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenis limbah ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-3 py-1.5 rounded-md border border-red-200 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>

                    {{-- Modal Edit --}}
                    <div class="modal fade" id="editWasteTypeModal{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('super-admin.waste-types.update', $item->id) }}" method="POST" class="modal-content">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title font-bold">Edit Jenis Limbah - {{ $item->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body space-y-3">
                                    <div>
                                        <label class="form-label">Kode Limbah</label>
                                        <input type="text" name="code" class="form-control" value="{{ $item->code }}" required>
                                    </div>
                                    <div>
                                        <label class="form-label">Nama Komoditas</label>
                                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                                    </div>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="form-label">Harga Acuan (Rp)</label>
                                            <input type="number" name="price_per_kg" class="form-control" value="{{ $item->price_per_kg }}" required>
                                        </div>
                                        <div>
                                            <label class="form-label">Satuan Unit</label>
                                            <select name="unit" class="form-select" required>
                                                <option value="kg" {{ $item->unit == 'kg' ? 'selected' : '' }}>Kilogram (kg)</option>
                                                <option value="liter" {{ $item->unit == 'liter' ? 'selected' : '' }}>Liter (L)</option>
                                                <option value="ton" {{ $item->unit == 'ton' ? 'selected' : '' }}>Ton</option>
                                                <option value="pcs" {{ $item->unit == 'pcs' ? 'selected' : '' }}>Pcs / Karung</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="form-label">Ikon Bootstrap (e.g. bi-trash, bi-box, bi-droplet)</label>
                                        <input type="text" name="icon" class="form-control" value="{{ $item->icon ?? 'bi-box' }}">
                                    </div>
                                    <div>
                                        <label class="form-label">Deskripsi</label>
                                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
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

{{-- Modal Tambah Jenis Limbah --}}
<div class="modal fade" id="addWasteTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.waste-types.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-bold">Tambah Jenis Limbah Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body space-y-3">
                <div>
                    <label class="form-label">Kode Limbah</label>
                    <input type="text" name="code" placeholder="Contoh: SML-01, SABUT-RAW" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Nama Komoditas Limbah</label>
                    <input type="text" name="name" placeholder="Contoh: Sabut Kelapa Mentah" class="form-control" required>
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="form-label">Harga Acuan (Rp)</label>
                        <input type="number" name="price_per_kg" placeholder="3500" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-semibold text-xs text-gray-700">Satuan Unit Utama (Satuan Tertinggi: Ton)</label>
                        <select name="unit" class="form-select border border-gray-300 rounded-lg text-sm" required>
                            <option value="ton" selected>Ton (Satuan Tertinggi Bulk / Tonase)</option>
                            <option value="kg">Kilogram (kg)</option>
                            <option value="liter">Liter (L)</option>
                            <option value="pcs">Pcs / Karung / Bal</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="form-label">Ikon Class (e.g. bi-trash, bi-droplet, bi-box)</label>
                    <input type="text" name="icon" value="bi-box" class="form-control">
                </div>
                <div>
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Catatan/spesifikasi limbah..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-emerald-600 text-white bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg font-semibold">Simpan Komoditas</button>
            </div>
        </form>
    </div>
</div>
@endsection

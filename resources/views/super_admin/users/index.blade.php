@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Manajemen Pengguna CoCoLink</h2>
            <p class="text-sm text-gray-500">Kelola akun, pendaftaran user baru, alokasi role, serta status verifikasi pengguna.</p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Tombol Ekspor PDF --}}
            <a href="{{ route('super-admin.users.export.pdf') }}" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1">
                <span>📄</span> Export PDF
            </a>

            {{-- Tombol Ekspor Excel --}}
            <a href="{{ route('super-admin.users.export.excel') }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1">
                <span>📊</span> Export Excel
            </a>

            {{-- Tombol Tambah User --}}
            <button data-bs-toggle="modal" data-bs-target="#addUserModal" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1">
                <span>➕</span> Tambah Pengguna
            </button>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('super-admin.users.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <input type="text" name="search" placeholder="Cari Nama, Email, atau Telepon..." value="{{ request('search') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">

            {{-- Filter Role --}}
            <select name="role" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Semua Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                        {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                    </option>
                @endforeach
            </select>

            {{-- Filter Status Verifikasi Email --}}
            <select name="verification" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Semua Status Verifikasi --</option>
                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>✓ Sudah Terverifikasi</option>
                <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>⏳ Belum Terverifikasi</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg w-full transition">Filter</button>
                <a href="{{ route('super-admin.users.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg text-center transition">Reset</a>
            </div>
        </form>
    </div>

    {{-- User Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">No</th>
                        <th class="p-4">Nama</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Status Verifikasi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-medium text-gray-400">{{ $users->firstItem() + $index }}</td>
                        <td class="p-4 font-bold text-gray-800">
                            {{ $user->name }}
                            @if($user->phone_number)
                                <div class="text-xs font-normal text-gray-400">{{ $user->phone_number }}</div>
                            @endif
                        </td>
                        <td class="p-4">{{ $user->email }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-800">
                                {{ strtoupper(str_replace('_', ' ', $user->roles->pluck('name')->first() ?? 'NO ROLE')) }}
                            </span>
                        </td>
                        {{-- Kolom Status Verifikasi & Tombol Kirim Ulang --}}
                        <td class="p-4">
                            @if($user->hasVerifiedEmail())
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-green-100 text-green-800 border border-green-200 inline-flex items-center gap-1">
                                    ✓ Terverifikasi
                                </span>
                            @else
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        ⏳ Belum Verifikasi
                                    </span>
                                    <form action="{{ route('super-admin.users.resend-verification', $user->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-[11px] bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium px-2 py-1 rounded border transition" title="Kirim Ulang Link Verifikasi">
                                            🔄 Kirim Ulang
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-right space-x-2">
                            <button data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}" class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-600 font-semibold px-3 py-1.5 rounded-md border border-blue-200 transition">
                                Edit
                            </button>
                            <form id="delete-user-form-{{ $user->id }}" action="{{ route('super-admin.users.destroy', $user->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-user-form-{{ $user->id }}', 'Hapus Pengguna {{ $user->name }}?', 'Akun dan hak akses role pengguna ini akan dihapus permanen.')" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 font-semibold px-3 py-1.5 rounded-md border border-red-200 transition">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-400 italic">Tidak ada data pengguna ditemukan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
    </div>
</div>

{{-- Modal Loop Edit Pengguna --}}
@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-labelledby="editUserModalLabel{{ $user->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.users.update', $user->id) }}" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title font-bold text-gray-800" id="editUserModalLabel{{ $user->id }}">Edit Pengguna - {{ $user->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-3 text-left">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $user->name }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $user->email }}" required>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone_number" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" value="{{ $user->phone_number }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password Baru <small class="text-gray-400">(Kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Role Pengguna</label>
                    <select name="role" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                            </option>
                        @endforeach
                    </select>
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

{{-- Modal Tambah Pengguna --}}
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('super-admin.users.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title font-bold text-gray-800" id="addUserModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="name" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required placeholder="Masukkan nama lengkap">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required placeholder="nama@domain.com">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">No. Telepon</label>
                    <input type="text" name="phone_number" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="081234567890">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required placeholder="Minimal 8 karakter">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Role Pengguna</label>
                    <select name="role" class="w-full text-sm border border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ strtoupper(str_replace('_', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-3 bg-emerald-50 rounded-lg border border-emerald-100 text-xs text-emerald-800">
                    ℹ️ Sistem akan secara otomatis mengoperasikan pendaftaran dan mengantar link verifikasi email ke pengguna baru.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-600 bg-gray-200 hover:bg-gray-300 rounded-lg transition" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition">Simpan & Kirim Verifikasi</button>
            </div>
        </form>
    </div>
</div>
@endsection

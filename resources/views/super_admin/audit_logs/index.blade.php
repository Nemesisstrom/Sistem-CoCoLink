@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Audit Logs & Activity Trail</h2>
            <p class="text-sm text-gray-500">Riwayat jejak aktivitas pengguna di seluruh ekosistem CoCoLink.</p>
        </div>
        <a href="{{ route('super-admin.audit-logs.export', request()->query()) }}" class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm flex items-center gap-1">
            <span>📥</span> Export Log (CSV)
        </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6">
        <form action="{{ route('super-admin.audit-logs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <input type="text" name="search" placeholder="Cari Nama Pengguna atau Deskripsi..." value="{{ request('search') }}" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">

            <select name="action" class="border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Semua Tipe Aksi --</option>
                <option value="CREATE_USER" {{ request('action') == 'CREATE_USER' ? 'selected' : '' }}>CREATE_USER</option>
                <option value="UPDATE_USER" {{ request('action') == 'UPDATE_USER' ? 'selected' : '' }}>UPDATE_USER</option>
                <option value="DELETE_USER" {{ request('action') == 'DELETE_USER' ? 'selected' : '' }}>DELETE_USER</option>
                <option value="ROLE_UPDATED" {{ request('action') == 'ROLE_UPDATED' ? 'selected' : '' }}>ROLE_UPDATED</option>
            </select>

            <div class="flex gap-2">
                <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold px-4 py-2 rounded-lg w-full transition">Cari</button>
                <a href="{{ route('super-admin.audit-logs.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg text-center transition">Reset</a>
            </div>
        </form>
    </div>

    <!-- Tabel Audit Logs -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                    <tr>
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Aksi</th>
                        <th class="p-4">Deskripsi Aktivitas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 whitespace-nowrap text-xs text-gray-400">
                            {{ $log->created_at ? $log->created_at->format('d M Y - H:i:s') : '-' }}
                        </td>
                        <td class="p-4 font-bold text-gray-800">{{ $log->user?->name ?? 'Sistem' }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-700">{{ $log->description }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-4 text-center text-gray-400 italic">Belum ada riwayat aktivitas yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection

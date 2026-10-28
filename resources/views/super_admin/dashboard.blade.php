@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 space-y-6">
    <!-- Header Dashboard -->
    <div class="flex flex-col md:flex-row justify-between md:items-center bg-white p-6 rounded-xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Super Admin Platform</h1>
            <p class="text-sm text-gray-500">Kelola pengguna, otorisasi role Spatie, dan audit trail aktivitas platform CoCoLink.</p>
        </div>
        <div class="flex gap-2">
            <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-2 rounded-lg flex items-center gap-1">
                🛡️ System Administrator
            </span>
        </div>
    </div>

    <!-- Ringkasan Metrik Platform -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase">Total User</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $totalUsers ?? 0 }}</p>
            </div>
            <span class="text-[10px] text-gray-500 font-medium mt-2">Pengguna Terdaftar</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase">Katalog Komoditas</p>
                <p class="text-2xl font-bold text-emerald-700 mt-1">{{ $totalWasteTypesCount ?? count($wasteTypes ?? []) }}</p>
            </div>
            <span class="text-[10px] text-emerald-600 font-medium mt-2">Jenis Limbah Aktif</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-blue-600 uppercase">Total Pesanan</p>
                <p class="text-2xl font-bold text-blue-700 mt-1">{{ $totalOrders ?? 0 }}</p>
            </div>
            <span class="text-[10px] text-blue-600 font-medium mt-2">Dalam Platform</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-green-600 uppercase">Pesanan Selesai</p>
                <p class="text-2xl font-bold text-green-700 mt-1">{{ $totalCompletedOrders ?? 0 }}</p>
            </div>
            <span class="text-[10px] text-green-600 font-medium mt-2">Transaksi Berhasil</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-emerald-600 uppercase">Nilai Transaksi</p>
                <p class="text-lg font-bold text-emerald-800 mt-1">Rp {{ number_format($totalRevenue ?? 0, 0, ',', '.') }}</p>
            </div>
            <span class="text-[10px] text-emerald-600 font-medium mt-2">Omset Platform</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-600 uppercase">Aktivitas Hari Ini</p>
                <p class="text-2xl font-bold text-amber-700 mt-1">{{ $todayActivityLogsCount ?? count($activityLogs ?? []) }}</p>
            </div>
            <span class="text-[10px] text-amber-600 font-medium mt-2">Audit Log Terekam</span>
        </div>
    </div>

    <!-- Grafik Ringkasan Aktivitas System (Data dilempar via HTML dataset agar aman dari linter JS) -->
    <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm">
        <h2 class="text-md font-bold text-gray-800 mb-4 flex items-center gap-2">
            <span>📊</span> Ringkasan Aktivitas Sistem (6 Bulan Terakhir)
        </h2>
        <div class="h-56">
            <canvas id="activityChart"
                data-labels="{{ json_encode($chartLabels ?? []) }}"
                data-values="{{ json_encode($chartData ?? []) }}">
            </canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tabel Pengguna & Otorisasi Role -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <span>👥</span> Manajemen Akses & Role Pengguna Spatie
                </h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                        <tr>
                            <th class="p-4">Nama</th>
                            <th class="p-4">Email</th>
                            <th class="p-4">Role Saat Ini</th>
                            <th class="p-4">Opsi Ubah Role</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50/50">
                            <td class="p-4 font-semibold text-gray-800">{{ $user->name }}</td>
                            <td class="p-4">{{ $user->email }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs rounded-full font-bold bg-emerald-100 text-emerald-800">
                                    {{ strtoupper(str_replace('_', ' ', $user->getRoleNames()->first() ?? 'No Role')) }}
                                </span>
                            </td>
                            <td class="p-4">
                                <form action="{{ route('super-admin.users.updateRole', $user->id) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="role" class="text-xs p-1.5 border rounded-md bg-gray-50">
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                                {{ strtoupper(str_replace('_', ' ', $role->name)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-gray-800 hover:bg-gray-900 text-white text-xs px-3 py-1.5 rounded-md transition">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Audit Trail / Activity Log Panel -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-4">
            <div class="flex justify-between items-center border-b pb-3">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-1.5">
                    <span>📜</span> Audit Trail Log
                </h2>
                <a href="{{ route('super-admin.audit-logs.export', request()->query()) }}" class="text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg transition shadow-sm flex items-center gap-1">
                    <span>📥</span> Ekspor CSV
                </a>
            </div>

            <form method="GET" action="{{ route('super-admin.dashboard') }}" class="space-y-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari aksi, deskripsi, atau nama user..." class="w-full text-xs p-2 border rounded-lg bg-gray-50">
                <div class="grid grid-cols-2 gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="text-xs p-1.5 border rounded-lg bg-gray-50">
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="text-xs p-1.5 border rounded-lg bg-gray-50">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="w-full bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold py-1.5 rounded-lg transition">Filter</button>
                    @if(request()->hasAny(['search', 'date_from', 'date_to']))
                        <a href="{{ route('super-admin.dashboard') }}" class="w-full bg-gray-200 text-gray-700 text-xs font-semibold py-1.5 rounded-lg text-center transition">Reset</a>
                    @endif
                </div>
            </form>

            <div class="space-y-3 max-h-[420px] overflow-y-auto pr-1">
                @forelse($activityLogs as $log)
                <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 text-xs space-y-1">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-emerald-700">{{ $log->action }}</span>
                        <span class="text-gray-400 text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-gray-700 font-medium">{{ $log->description }}</p>
                    <div class="flex justify-between items-center text-[10px] text-gray-400 pt-1 border-t border-gray-100">
                        <span>By: {{ $log->user->name ?? 'System' }}</span>
                        <span>IP: {{ $log->ip_address ?? 'N/A' }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 italic text-center py-4">Tidak ada catatan aktivitas yang cocok.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Script Chart.js Murni Tanpa Tag Blade di Dalamnya -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const canvas = document.getElementById('activityChart');
        if (!canvas) return;

        const chartLabels = JSON.parse(canvas.dataset.labels || '[]');
        const chartValues = JSON.parse(canvas.dataset.values || '[]');

        const ctx = canvas.getContext('2d');
        if (typeof window.Chart !== 'undefined') {
            new window.Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [{
                        label: 'Jumlah Aktivitas Tercatat',
                        data: chartValues,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    });
</script>
@endsection

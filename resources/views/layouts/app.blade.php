<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'CoCoLink') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased text-gray-800">
    <nav class="bg-emerald-700 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo & Links --}}
                <div class="flex items-center gap-8">
                    <a href="/" class="text-xl font-bold tracking-wider text-emerald-100 flex items-center gap-2">
                        <span>🌴</span> CoCoLink
                    </a>

                    {{-- Navigasi Utama Berdasarkan Role --}}
                    @auth
                        <div class="hidden md:flex items-center gap-4 text-sm font-medium">
                            @hasrole('super_admin')
                                <a href="{{ route('super-admin.dashboard') }}"
                                   class="px-3 py-2 rounded-md transition {{ request()->routeIs('super-admin.dashboard') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-100 hover:bg-emerald-600' }}">
                                    Dashboard
                                </a>

                                {{-- Manajemen User + Badge Total Pengguna --}}
                                <a href="{{ route('super-admin.users.index') }}"
                                   class="px-3 py-2 rounded-md transition flex items-center gap-2 {{ request()->routeIs('super-admin.users.*') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-100 hover:bg-emerald-600' }}">
                                    <span>Manajemen User</span>
                                    <span class="bg-emerald-900 text-emerald-200 text-xs font-semibold px-2 py-0.5 rounded-full border border-emerald-500">
                                        {{ $totalUsersCount ?? 0 }}
                                    </span>
                                </a>

                                <a href="{{ route('super-admin.waste-types.index') }}"
                                   class="px-3 py-2 rounded-md transition {{ request()->routeIs('super-admin.waste-types.*') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-100 hover:bg-emerald-600' }}">
                                    Jenis Limbah
                                </a>

                                <a href="{{ route('super-admin.audit-logs.index') }}"
                                   class="px-3 py-2 rounded-md transition {{ request()->routeIs('super-admin.audit-logs.*') ? 'bg-emerald-800 text-white font-bold' : 'text-emerald-100 hover:bg-emerald-600' }}">
                                    Audit Logs
                                </a>
                            @endhasrole
                        </div>
                    @endauth
                </div>

                {{-- User Status & Logout --}}
                <div class="flex items-center gap-4">
                    @auth
                        <span class="text-sm bg-emerald-800 px-3 py-1 rounded-full text-emerald-200">
                            {{ auth()->user()->name }} ({{ strtoupper(str_replace('_', ' ', auth()->user()->getRoleNames()->first() ?? 'User')) }})
                        </span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-md transition">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm hover:underline">Login</a>
                        <a href="{{ route('register') }}" class="text-sm bg-emerald-600 hover:bg-emerald-500 px-3 py-1.5 rounded-md font-semibold text-white transition">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        @yield('content')
    </main>

    <!-- Bootstrap 5 Bundle JS (Untuk mengaktifkan fungsi Modal) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- Notification Script & Confirm Helper --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: "{{ session('error') }}",
                    confirmButtonColor: '#059669',
                });
            @endif
        });

        function confirmDelete(formId, titleText = 'Apakah Anda yakin?', descriptionText = 'Data yang dihapus tidak dapat dikembalikan!') {
            Swal.fire({
                title: titleText,
                text: descriptionText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel') }} - CoCoLink</title>

        <!-- Alpine.js untuk Interaktivitas Kalkulator & FAQ -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        @fonts

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <style>
                /*! tailwindcss v4.0.7 | MIT License | https://tailwindcss.com */ @layer properties{@supports (((-webkit-hyphens:none)) and (not (margin-trim:inline))) or ((-moz-orient:inline) and (not (color:rgb(from red r g b)))){*,:before,:after,::backdrop{--tw-translate-x:0;--tw-translate-y:0;--tw-translate-z:0;--tw-rotate-x:initial;--tw-rotate-y:initial;--tw-rotate-z:initial;--tw-skew-x:initial;--tw-skew-y:initial;--tw-space-x-reverse:0;--tw-border-style:solid;--tw-leading:initial;--tw-font-weight:initial;--tw-tracking:initial;--tw-shadow:0 0 #0000;--tw-shadow-color:initial;--tw-shadow-alpha:100%;--tw-inset-shadow:0 0 #0000;--tw-inset-shadow-color:initial;--tw-inset-shadow-alpha:100%;--tw-ring-color:initial;--tw-ring-shadow:0 0 #0000;--tw-inset-ring-color:initial;--tw-inset-ring-shadow:0 0 #0000;--tw-ring-inset:initial;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-offset-shadow:0 0 #0000;--tw-blur:initial;--tw-brightness:initial;--tw-contrast:initial;--tw-grayscale:initial;--tw-hue-rotate:initial;--tw-invert:initial;--tw-opacity:initial;--tw-saturate:initial;--tw-sepia:initial;--tw-drop-shadow:initial;--tw-drop-shadow-color:initial;--tw-drop-shadow-alpha:100%;--tw-drop-shadow-size:initial;--tw-duration:initial;--tw-ease:initial;--tw-content:""}}}@layer theme{:root,:host{--font-sans:"Instrument Sans", ui-sans-serif, system-ui, sans-serif;--color-gray-50:oklch(98.5% .002 247.839);--color-gray-100:oklch(96.7% .003 264.542);--color-gray-200:oklch(92.8% .006 264.531);--color-gray-300:oklch(87.2% .01 258.338);--color-gray-400:oklch(70.7% .022 261.325);--color-gray-500:oklch(55.1% .027 264.364);--color-gray-600:oklch(44.6% .03 256.802);--color-gray-700:oklch(37.3% .034 259.733);--color-gray-800:oklch(27.8% .033 256.848);--color-gray-900:oklch(21% .034 264.665);--color-black:#000;--color-white:#fff;--spacing:.25rem}}@layer base{*,:after,:before,::backdrop{box-sizing:border-box;border:0 solid;margin:0;padding:0}html,:host{-webkit-text-size-adjust:100%;tab-size:4;line-height:1.5;font-family:var(--font-sans)}table{text-indent:0;border-color:inherit;border-collapse:collapse}ol,ul,menu{list-style:none}img,svg,video,canvas,audio,iframe,embed,object{display:block}img,video{max-width:100%;height:auto}}
            </style>
        @endif
    </head>
    <body class="bg-[#FDFDFC] text-[#1b1b18] flex p-6 lg:p-12 items-center justify-center min-h-screen flex-col">

        <!-- Header / Navigation -->
        <header class="w-full max-w-5xl text-sm mb-6 flex justify-between items-center">
            <div class="font-bold text-lg text-emerald-800 flex items-center gap-2">
                <span>🥥</span> CoCoLink Platform
            </div>
            @if (Route::has('login'))
                <nav class="flex items-center gap-4">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-block px-5 py-2 bg-emerald-700 text-white hover:bg-emerald-800 rounded-lg text-sm font-semibold transition shadow-sm"
                        >
                            Dashboard
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-block px-4 py-2 text-gray-700 hover:text-emerald-700 text-sm font-medium transition"
                        >
                            Log in
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-block px-5 py-2 bg-emerald-700 text-white hover:bg-emerald-800 rounded-lg text-sm font-semibold transition shadow-sm">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            @endif
        </header>

        <!-- Main Content Area -->
        <div class="w-full max-w-5xl space-y-8">

            <!-- Hero Section -->
            <main class="bg-white p-8 sm:p-12 rounded-3xl shadow-sm border border-gray-100 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="space-y-4 max-w-xl">
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-3 py-1.5 rounded-full uppercase tracking-wider">
                        Sinergi Rantai Pasok Limbah Kelapa
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-gray-900 leading-tight">
                        Optimalisasi Logistik & Nilai Ekonomi Limbah Kelapa
                    </h1>
                    <p class="text-sm sm:text-base text-gray-600 leading-relaxed">
                        CoCoLink menghubungkan Koperasi penyedia komoditas limbah kelapa (Sabut, Tempurung, Cocopeat) dengan industri pengolahan melalui sistem manajemen rantai pasok dan logistik transparan.
                    </p>
                </div>
                <div class="bg-emerald-50 border border-emerald-100 p-6 rounded-2xl text-center shrink-0 w-full md:w-72">
                    <span class="text-3xl">🌱</span>
                    <h3 class="font-bold text-emerald-900 mt-2">Mitra Terverifikasi</h3>
                    <p class="text-xs text-emerald-700 mt-1">Sistem otorisasi role Spatie & audit trail terintegrasi penuh.</p>
                </div>
            </main>

            <!-- 1. Kalkulator Estimasi Limbah Kelapa (Interactive) -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100" x-data="{
                wasteType: 'cocofiber',
                weight: 100,
                prices: { cocofiber: 3500, tempurung: 2500, cocopeat: 1500 },
                get totalValue() { return this.weight * this.prices[this.wasteType]; }
            }">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xl">🧮</span>
                    <h2 class="text-xl font-bold text-gray-800">Kalkulator Estimasi Nilai Limbah Kelapa</h2>
                </div>
                <p class="text-sm text-gray-500 mb-6">Simulasikan perkiraan nilai ekonomi pasokan limbah komoditas Anda secara instan.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Form Input -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Komoditas Limbah</label>
                            <select x-model="wasteType" class="w-full text-xs p-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                                <option value="cocofiber">Serat Sabut Kelapa (Cocofiber)</option>
                                <option value="tempurung">Tempurung Kelapa (Shell)</option>
                                <option value="cocopeat">Serbuk Sabut Kelapa (Cocopeat)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Estimasi Berat / Volume (Kg)</label>
                            <input type="number" x-model.number="weight" min="1" class="w-full text-xs p-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-emerald-500">
                        </div>
                    </div>

                    <!-- Result Box -->
                    <div class="bg-emerald-50 border border-emerald-100 rounded-2xl p-6 flex flex-col justify-between">
                        <div>
                            <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Estimasi Nilai Potensial</span>
                            <p class="text-3xl font-extrabold text-emerald-900 mt-2">
                                Rp <span x-text="totalValue.toLocaleString('id-ID')"></span>
                            </p>
                        </div>
                        <p class="text-[11px] text-emerald-600 mt-4">*Harga estimasi berdasarkan rata-rata pasar industri pengolahan mitra CoCoLink.</p>
                    </div>
                </div>
            </div>

            <!-- 2. Bagian FAQ Interaktif (Accordion) -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 space-y-4" x-data="{ activeAccordion: null }">
                <div class="flex items-center gap-2 mb-4">
                    <span class="text-xl">❓</span>
                    <h2 class="text-xl font-bold text-gray-800">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                </div>

                <!-- Item FAQ 1 -->
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full flex justify-between items-center p-4 text-left font-semibold text-sm text-gray-800 bg-gray-50 hover:bg-gray-100 transition">
                        <span>Bagaimana cara Koperasi bergabung dalam ekosistem CoCoLink?</span>
                        <span x-text="activeAccordion === 1 ? '−' : '+'" class="text-lg font-bold"></span>
                    </button>
                    <div x-show="activeAccordion === 1" class="p-4 text-xs text-gray-600 bg-white border-t border-gray-100">
                        Koperasi dapat mendaftar melalui platform, lalu Super Admin akan memverifikasi data unit dan wilayah operasional untuk memberikan akses modul pasokan dan manajemen stok harian.
                    </div>
                </div>

                <!-- Item FAQ 2 -->
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full flex justify-between items-center p-4 text-left font-semibold text-sm text-gray-800 bg-gray-50 hover:bg-gray-100 transition">
                        <span>Bagaimana sistem pelacakan pengiriman limbah oleh Driver Logistik?</span>
                        <span x-text="activeAccordion === 2 ? '−' : '+'" class="text-lg font-bold"></span>
                    </button>
                    <div x-show="activeAccordion === 2" class="p-4 text-xs text-gray-600 bg-white border-t border-gray-100">
                        Driver logistik menerima penugasan otomatis melalui dashboard, memperbarui status perjalanan dari penjemputan hingga pengantaran, dan mengunggah Bukti Pengiriman (*Proof of Delivery*).
                    </div>
                </div>
            </div>

            <!-- Footer Info -->
            <footer class="text-center text-xs text-gray-400 py-6">
                Laravel v{{ app()->version() }} &bull; CoCoLink Platform Sinergi Limbah Kelapa &copy; 2026
            </footer>
        </div>
    </body>
</html>

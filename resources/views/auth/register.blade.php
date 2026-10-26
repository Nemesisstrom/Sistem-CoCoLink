@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Pendaftaran Mitra CoCoLink</h2>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Daftar Sebagai</label>
            <select name="role" id="roleSelect" onchange="toggleDriverField()" class="mt-1 w-full p-2.5 border rounded-md focus:ring-emerald-500 focus:border-emerald-500">
                <option value="pengepul" {{ old('role') == 'pengepul' ? 'selected' : '' }}>Pembeli Industri / Pengepul (PT/CV)</option>
                <option value="driver" {{ old('role') == 'driver' ? 'selected' : '' }}>Mitra Logistik / Driver</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nama Lengkap / Nama Perusahaan</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="mt-1 w-full p-2.5 border rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full p-2.5 border rounded-md">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Kata Sandi</label>
                <input type="password" name="password" required class="mt-1 w-full p-2.5 border rounded-md">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Konfirmasi Sandi</label>
                <input type="password" name="password_confirmation" required class="mt-1 w-full p-2.5 border rounded-md">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Nomor Telepon/WA</label>
            <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="mt-1 w-full p-2.5 border rounded-md">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
            <textarea name="address" rows="2" required class="mt-1 w-full p-2.5 border rounded-md">{{ old('address') }}</textarea>
        </div>
        <div id="driverField" class="hidden">
            <label class="block text-sm font-medium text-gray-700">Nomor Plat Kendaraan</label>
            <input type="text" name="vehicle_number" placeholder="Contoh: B 1234 COCO" value="{{ old('vehicle_number') }}" class="mt-1 w-full p-2.5 border rounded-md">
        </div>
        <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-md hover:bg-emerald-700 transition font-semibold">Selesaikan Pendaftaran</button>
    </form>
</div>

<script>
    function toggleDriverField() {
        const role = document.getElementById('roleSelect').value;
        const driverField = document.getElementById('driverField');
        if (role === 'driver') {
            driverField.classList.remove('hidden');
        } else {
            driverField.classList.add('hidden');
        }
    }
    document.addEventListener("DOMContentLoaded", toggleDriverField);
</script>
@endsection

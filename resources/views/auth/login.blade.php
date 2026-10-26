@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Login CoCoLink</h2>
    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700">Alamat Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="mt-1 w-full p-2.5 border rounded-md focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700">Kata Sandi</label>
            <input type="password" name="password" required class="mt-1 w-full p-2.5 border rounded-md focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div class="flex items-center justify-between">
            <label class="flex items-center text-sm text-gray-600">
                <input type="checkbox" name="remember" class="rounded text-emerald-600 mr-2"> Ingat Saya
            </label>
        </div>
        <button type="submit" class="w-full bg-emerald-600 text-white py-2.5 rounded-md hover:bg-emerald-700 transition font-semibold">Masuk Akun</button>
    </form>
    <p class="mt-4 text-center text-sm text-gray-600">Belum punya akun? <a href="{{ route('register') }}" class="text-emerald-600 font-semibold hover:underline">Daftar Pengepul/Driver</a></p>
</div>
@endsection

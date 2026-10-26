<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    // Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Proses Login & Redirection berdasarkan Role
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->hasRole('koperasi_induk')) {
                return redirect()->intended(route('koperasi-induk.dashboard'));
            } elseif ($user->hasRole('koperasi_anak')) {
                return redirect()->intended(route('koperasi-anak.dashboard'));
            } elseif ($user->hasRole('pengepul')) {
                return redirect()->intended(route('pengepul.dashboard'));
            } elseif ($user->hasRole('driver')) {
                return redirect()->intended(route('driver.dashboard'));
            }

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => 'Kredensial yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    // Form Register Pengepul / Driver
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    // Proses Registrasi
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:pengepul,driver',
            'phone_number' => 'required|string|max:15',
            'address' => 'required|string',
            'vehicle_number' => 'required_if:role,driver|nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'vehicle_number' => $request->role === 'driver' ? $request->vehicle_number : null,
        ]);

        // Assign Spatie Role
        $user->assignRole($request->role);

        Auth::login($user);

        return redirect()->route($request->role . '.dashboard');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

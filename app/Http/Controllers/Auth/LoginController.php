<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            if ($user->hasRole('koperasi_induk')) {
                return redirect()->intended('/koperasi-induk/dashboard');
            } elseif ($user->hasRole('koperasi_anak')) {
                return redirect()->intended('/koperasi-anak/dashboard');
            } elseif ($user->hasRole('pengepul')) {
                return redirect()->intended('/pengepul/dashboard');
            } elseif ($user->hasRole('driver')) {
                return redirect()->intended('/driver/dashboard');
            }

            return redirect('/');
        }

        return back()->withErrors(['email' => 'Kredensial tidak valid.']);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Kalau sudah login, jangan paksa redirect: logout dulu supaya bisa ganti akun
        if (Auth::check()) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Buang sesi lama (misal pemilik) sebelum login akun baru
        Auth::logout();

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended($this->homeByRole(Auth::user()->role));
        }

        return back()->withErrors(['email' => 'Email atau password salah'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function homeByRole(string $role): string
    {
        return match ($role) {
            'admin'   => '/admin/dashboard',
            'pemilik' => '/pemilik/dashboard',
            'kasir'   => '/kasir',
            default   => '/login',
        };
    }
}
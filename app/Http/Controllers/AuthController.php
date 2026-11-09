<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan form login
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi user (username atau email)
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username atau email wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $fieldType = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $remember = $request->boolean('remember');

        if (Auth::attempt([$fieldType => $credentials['login'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
        }

        return back()->withErrors([
            'login' => 'Username atau kata sandi yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    /**
     * Logout user
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah berhasil keluar dari sistem kasir.');
    }
}

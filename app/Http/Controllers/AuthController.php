<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $role = Auth::user()->role;
            if ($role === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            if ($role === 'pengepul') {
                return redirect()->route('pengepul.dashboard');
            }
            return redirect()->route('petani.dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Username, Email, atau Nomor WhatsApp wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $loginInput = $credentials['login'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        // Tentukan login field: email, no_hp, atau username
        $fieldType = 'username';
        if (filter_var($loginInput, FILTER_VALIDATE_EMAIL)) {
            $fieldType = 'email';
        } elseif (preg_match('/^[0-9]+$/', $loginInput)) {
            $fieldType = 'no_hp';
        }

        if (Auth::attempt([$fieldType => $loginInput, 'password' => $password], $remember)) {
            $request->session()->regenerate();

            $user = Auth::user();
            if ($user->role === 'admin') {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang, Administrator ' . $user->name . '!');
            }
            if ($user->role === 'pengepul') {
                return redirect()->intended(route('pengepul.dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
            }

            return redirect()->intended(route('petani.dashboard'))
                ->with('success', 'Selamat datang, ' . $user->name . '!');
        }

        // Coba alternatif jika input nomor dengan 08 / 62
        if ($fieldType === 'no_hp') {
            $altPhone = str_starts_with($loginInput, '0') 
                ? '62' . substr($loginInput, 1) 
                : (str_starts_with($loginInput, '62') ? '0' . substr($loginInput, 2) : $loginInput);

            if (Auth::attempt(['no_hp' => $altPhone, 'password' => $password], $remember)) {
                $request->session()->regenerate();
                return redirect()->intended(route('petani.dashboard'));
            }
        }

        return back()->withErrors([
            'login' => 'Kombinasi akun atau password tidak cocok.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil logout.');
    }
}

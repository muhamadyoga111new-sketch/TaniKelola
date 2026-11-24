<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Halaman Profil Saya: data akun yang sedang login.
     * Semua peran (admin, pengepul, petani) memakai halaman yang sama.
     * Username dan peran tidak bisa diubah sendiri: username adalah
     * identitas login, peran hanya bisa diubah admin.
     */
    public function show(Request $request)
    {
        return view('profile.show', ['user' => $request->user()]);
    }

    /** Halaman Ubah Data Profil (form saja, data tidak tampil di halaman lihat). */
    public function edit(Request $request)
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    /** Halaman Ganti Password (form saja). */
    public function editPassword(Request $request)
    {
        return view('profile.password', ['user' => $request->user()]);
    }

    /**
     * Ubah nama, email, dan nomor HP akun sendiri.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('pengguna', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.email' => 'Format email tidak benar.',
            'email.unique' => 'Email sudah dipakai akun lain.',
        ]);

        $user->update([
            'nama' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'no_hp' => $validated['phone'] ?? null,
        ]);

        return back()->with('success', 'Data profil berhasil disimpan.');
    }

    /**
     * Ganti password akun sendiri. Password lama harus benar.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'current_password.current_password' => 'Password lama tidak cocok.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password berhasil diganti. Gunakan password baru saat login berikutnya.');
    }
}

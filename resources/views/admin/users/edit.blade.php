@extends('layouts.app')

@section('title', 'Edit Akun - ' . $user->name)
@section('page_title', 'Edit Akun Pengguna')
@section('page_subtitle', 'Perbarui informasi akun ' . $user->name)

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-slate-800">Edit Akun: {{ $user->name }}</h3>
                <p class="text-xs text-slate-500 mt-0.5">Perbarui data akun pengguna ini. Kosongkan password jika tidak ingin diubah.</p>
            </div>
            <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold border
                @if($user->role === 'admin') bg-purple-50 text-purple-700 border-purple-200
                @elseif($user->role === 'pengepul') bg-amber-50 text-amber-700 border-amber-200
                @else bg-emerald-50 text-emerald-700 border-emerald-200
                @endif">
                {{ ucfirst($user->role) }}
            </span>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Role Selection -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Role / Peran <span class="text-rose-500">*</span></label>
                <select name="role" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin (Administrator)</option>
                    <option value="pengepul" {{ old('role', $user->role) === 'pengepul' ? 'selected' : '' }}>Pengepul</option>
                    <option value="petani" {{ old('role', $user->role) === 'petani' ? 'selected' : '' }}>Petani Mitra</option>
                </select>
                @error('role') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                @error('name') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <!-- Username -->
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username <span class="text-rose-500">*</span></label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                @error('username') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Email -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('email') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">No. Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    @error('phone') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Password (Optional on edit) -->
            <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                <p class="text-xs font-bold text-amber-700 mb-3">Ubah Password (Opsional)</p>
                <p class="text-[11px] text-amber-600 mb-3">Kosongkan kedua field di bawah jika tidak ingin mengubah password.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password Baru</label>
                        <input type="password" name="password"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent bg-white">
                        @error('password') <p class="text-rose-500 text-[11px] mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            placeholder="Ulangi password baru"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent bg-white">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">
                    ← Kembali
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

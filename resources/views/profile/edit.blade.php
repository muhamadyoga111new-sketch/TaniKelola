@extends('layouts.app')

@section('title', 'Ubah Data Profil')
@section('page_title', 'Ubah Data Profil')
@section('page_subtitle', 'Nama, email, dan nomor HP')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <section class="rounded-lg bg-white border border-slate-200 p-5 sm:p-6">
        <p class="text-xs text-slate-500">Username tetap, karena dipakai untuk login.</p>
        <form action="{{ route('profile.update') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required maxlength="150" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                @error('name') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" maxlength="150" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                @error('email') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700">No. HP / WhatsApp</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" maxlength="20" inputmode="tel" class="mt-1 w-full rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                @error('phone') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-md bg-[#14532d] text-white text-sm font-bold hover:bg-[#0f3d21] transition">Simpan Perubahan</button>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2.5 rounded-md border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">Kembali ke Profil</a>
            </div>
        </form>
    </section>
</div>
@endsection

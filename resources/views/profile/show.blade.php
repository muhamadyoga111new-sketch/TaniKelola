@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Saya')
@section('page_subtitle', 'Data akun yang sedang login')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Kartu identitas akun --}}
    <section class="rounded-lg bg-[#14532d] border-b-4 border-[#eab308] px-5 py-6 sm:px-7">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 shrink-0 rounded-full bg-[#eab308] text-[#14532d] flex items-center justify-center font-extrabold text-2xl uppercase">
                {{ substr($user->name ?? 'U', 0, 1) }}
            </div>
            <div class="min-w-0">
                <h2 class="text-xl font-extrabold text-white truncate">{{ str_replace([' (Pengepul)', ' (Admin)', ' (Petani)'], '', $user->name) }}</h2>
                <p class="text-xs text-white/75">@ {{ $user->username }}</p>
            </div>
        </div>
        <dl class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-3 text-xs">
            <div>
                <dt class="font-bold uppercase tracking-wider text-[#eab308]">Email</dt>
                <dd class="mt-0.5 text-white truncate">{{ $user->email ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-bold uppercase tracking-wider text-[#eab308]">No. HP / WA</dt>
                <dd class="mt-0.5 text-white">{{ $user->phone ?? '-' }}</dd>
            </div>
            <div>
                <dt class="font-bold uppercase tracking-wider text-[#eab308]">Peran</dt>
                <dd class="mt-0.5 text-white capitalize">{{ $user->role }}</dd>
            </div>
        </dl>
        <p class="mt-4 text-xs font-medium text-white">Username dan peran tidak bisa diubah di sini. Peran hanya bisa diubah admin.</p>
    </section>

    {{-- Aksi profil: form hanya muncul setelah tombol diklik. --}}
    <section class="rounded-lg bg-white border border-slate-200 p-5 sm:p-6">
        <h3 class="text-base font-extrabold text-[#14532d]">Kelola Akun</h3>
        <p class="mt-1 text-xs text-slate-500">Ubah data atau ganti password lewat halaman tersendiri.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-4 py-2.5 rounded-md bg-[#14532d] text-white text-sm font-bold hover:bg-[#0f3d21] transition">Ubah Data Profil</a>
            <a href="{{ route('profile.password.edit') }}" class="inline-flex items-center px-4 py-2.5 rounded-md border border-[#14532d] text-[#14532d] text-sm font-bold hover:bg-[#14532d]/5 transition">Ganti Password</a>
        </div>
    </section>

    {{-- Keluar: hanya ada di halaman profil, bukan di navbar. --}}
    <section class="rounded-lg bg-white border border-slate-200 p-5 sm:p-6">
        <h3 class="text-base font-extrabold text-slate-900">Keluar</h3>
        <p class="mt-1 text-xs text-slate-500">Keluar dari akun {{ str_replace([' (Pengepul)', ' (Admin)', ' (Petani)'], '', $user->name) }} di perangkat ini.</p>
        <form action="{{ route('logout') }}" method="POST" class="mt-4">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-md bg-rose-600 text-white text-sm font-bold hover:bg-rose-700 transition">Keluar dari Akun</button>
        </form>
    </section>

</div>
@endsection

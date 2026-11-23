@extends('layouts.app')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard Administrator')
@section('page_subtitle', 'Ringkasan keseluruhan sistem TaniKelola')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Welcome Banner -->
    <div class="p-4 sm:p-6 rounded-2xl sm:rounded-3xl bg-gradient-to-r from-slate-800 via-indigo-900 to-slate-900 text-white shadow-xl relative overflow-hidden">
        <div class="absolute right-0 top-0 bottom-0 w-1/3 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-white/10 to-transparent pointer-events-none"></div>
        <div class="relative z-10">
            <p class="text-xs text-indigo-200 mb-1">Panel Administrator</p>
            <h3 class="text-lg sm:text-2xl font-bold tracking-tight">Selamat Datang, {{ auth()->user()->name }}</h3>
            <p class="text-xs text-indigo-100/80 mt-1 max-w-xl hidden sm:block">
                Kelola akun pengguna, pantau aktivitas sistem, dan kontrol keseluruhan TaniKelola dari sini.
            </p>
            <div class="flex flex-wrap gap-2 mt-3">
                <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-xs shadow-md transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                    <span>Tambah Akun</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="inline-flex items-center space-x-1.5 px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-white font-semibold text-xs border border-white/20 transition">
                    <span>Lihat Semua Akun</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Pengguna -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $totalUsers }}</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Total Pengguna</p>
        </div>

        <!-- Admin -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-purple-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $totalAdmin }}</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Administrator</p>
        </div>

        <!-- Pengepul -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $totalPengepul }}</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Pengepul</p>
        </div>

        <!-- Petani Aktif -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs hover:shadow-md transition">
            <div class="flex items-center justify-between mb-2">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <p class="text-xl sm:text-2xl font-extrabold text-slate-900">{{ $totalPetani }}</p>
            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Petani Mitra Aktif</p>
        </div>
    </div>

    <!-- Financial Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Modal Disalurkan</p>
            <p class="text-xl font-extrabold text-blue-600">Rp {{ number_format($totalModalDiberikan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Total Pembayaran Kewajiban</p>
            <p class="text-xl font-extrabold text-emerald-600">Rp {{ number_format($totalPembayaranKewajiban, 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-1">Sisa Kewajiban Petani</p>
            <p class="text-xl font-extrabold text-rose-600">Rp {{ number_format($totalSisaKewajiban, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Recent Users & Petani Kewajiban -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Akun Terbaru -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-bold text-sm text-slate-900">Akun Terbaru</h4>
                <a href="{{ route('admin.users.index') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($usersTerbaru as $u)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs uppercase shrink-0
                                @if($u->role === 'admin') bg-purple-100 text-purple-700
                                @elseif($u->role === 'pengepul') bg-amber-100 text-amber-700
                                @else bg-emerald-100 text-emerald-700
                                @endif">
                                {{ substr($u->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-800">{{ $u->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $u->username }} · {{ $u->role }}</p>
                            </div>
                        </div>
                        <span class="text-[10px] px-2 py-0.5 rounded-full font-semibold border
                            @if($u->role === 'admin') bg-purple-50 text-purple-700 border-purple-200
                            @elseif($u->role === 'pengepul') bg-amber-50 text-amber-700 border-amber-200
                            @else bg-emerald-50 text-emerald-700 border-emerald-200
                            @endif">
                            {{ ucfirst($u->role) }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-400">Belum ada akun terdaftar.</div>
                @endforelse
            </div>
        </div>

        <!-- Petani Kewajiban Tertinggi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h4 class="font-bold text-sm text-slate-900">Petani - Sisa Kewajiban Tertinggi</h4>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse($petaniKewajiban as $pk)
                    <div class="px-5 py-3 flex items-center justify-between">
                        <div>
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-semibold text-slate-800">{{ $pk->nama_petani }}</p>
                                <span class="font-mono text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                    {{ $pk->kode_petani }}
                                </span>
                            </div>
                        </div>
                        <span class="text-sm font-bold text-rose-600">Rp {{ number_format($pk->sisa_kewajiban, 0, ',', '.') }}</span>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-slate-400">Tidak ada petani dengan kewajiban.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Profil Belum Terhubung')
@section('page_title', 'Profil Petani Belum Ditemukan')

@section('content')
<div class="max-w-md mx-auto py-12 text-center">
    <div class="w-16 h-16 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-3xl mx-auto mb-4">
        ⚠️
    </div>
    <h3 class="text-xl font-bold text-slate-800">Akun Belum Terhubung Profil Petani</h3>
    <p class="text-sm text-slate-500 mt-2 mb-6">
        Akun Anda (<strong>{{ $user->username }}</strong>) belum ditautkan dengan data petani oleh Bapak Mohtar (Pengepul). Silakan konfirmasi ke pihak pengepul agar profil Anda dihubungkan.
    </p>
    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="px-5 py-2.5 rounded-xl bg-slate-800 text-white font-bold text-xs">
        Keluar
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">@csrf</form>
</div>
@endsection

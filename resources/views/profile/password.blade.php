@extends('layouts.app')

@section('title', 'Ganti Password')
@section('page_title', 'Ganti Password')
@section('page_subtitle', 'Password baru minimal 6 karakter')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">
    <section class="rounded-lg bg-white border border-slate-200 p-5 sm:p-6">
        <p class="text-xs text-slate-500">Setelah diganti, login berikutnya memakai password baru.</p>
        <form action="{{ route('profile.password') }}" method="POST" class="mt-4 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-700">Password Lama</label>
                <div class="relative">
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password" class="mt-1 w-full pr-10 rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#14532d] focus:outline-none" data-target="current_password" title="Tampilkan / Sembunyikan Password" aria-label="Tampilkan / Sembunyikan Password">
                        <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-off-icon w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
            </div>
                @error('current_password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700">Password Baru</label>
                <div class="relative">
                <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password" class="mt-1 w-full pr-10 rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#14532d] focus:outline-none" data-target="password" title="Tampilkan / Sembunyikan Password" aria-label="Tampilkan / Sembunyikan Password">
                        <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-off-icon w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
            </div>
                @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
            </div>
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700">Konfirmasi Password Baru</label>
                <div class="relative">
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="6" autocomplete="new-password" class="mt-1 w-full pr-10 rounded-md border border-slate-300 px-3 py-2 text-sm focus:border-[#14532d] focus:ring-1 focus:ring-[#14532d] focus:outline-none">
                <button type="button" class="toggle-password absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-[#14532d] focus:outline-none" data-target="password_confirmation" title="Tampilkan / Sembunyikan Password" aria-label="Tampilkan / Sembunyikan Password">
                        <svg class="eye-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        <svg class="eye-off-icon w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                    </button>
            </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2.5 rounded-md bg-[#eab308] text-[#14532d] text-sm font-extrabold hover:bg-[#facc15] transition">Ganti Password</button>
                <a href="{{ route('profile.show') }}" class="inline-flex items-center px-4 py-2.5 rounded-md border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-50 transition">Kembali ke Profil</a>
            </div>
        </form>
    </section>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.toggle-password').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.getAttribute('data-target'));
            if (!input) return;
            var show = input.getAttribute('type') === 'password';
            input.setAttribute('type', show ? 'text' : 'password');
            btn.querySelector('.eye-icon').classList.toggle('hidden', show);
            btn.querySelector('.eye-off-icon').classList.toggle('hidden', !show);
        });
    });
</script>
@endpush

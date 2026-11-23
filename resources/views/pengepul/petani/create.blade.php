@extends('layouts.app')

@section('title', 'Tambah Petani Mitra')
@section('page_title', 'Pendaftaran Petani Mitra')
@section('page_subtitle', 'Tambahkan data petani baru serta pembuatan akun login sistem')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.petani.store') }}" method="POST" class="space-y-6">
            @csrf

            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-base text-slate-900">1. Data Profil Petani</h3>
                <p class="text-xs text-slate-500">Informasi identitas dan lokasi budidaya di Desa Bajuin.</p>
            </div>

            <input type="hidden" name="kode_petani" value="{{ old('kode_petani', $suggestedCode) }}">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Petani *</label>
                    <input type="text" name="nama_petani" value="{{ old('nama_petani') }}" placeholder="Contoh: Pak Jumadi" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa') }}" placeholder="Contoh: 082155667788" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Nomor ini akan menerima pesan notifikasi otomatis dari Fonnte.</span>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIK (Opsional)</label>
                    <input type="text" name="nik" value="{{ old('nik') }}" placeholder="16 digit NIK KTP"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Luas Lahan</label>
                    <input type="text" name="luas_lahan" value="{{ old('luas_lahan') }}" placeholder="Contoh: 0.5 Hektar (10 borong)"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Komoditas Utama Sayuran</label>
                    <input type="text" name="komoditas_utama" value="{{ old('komoditas_utama') }}" placeholder="Contoh: Cabai Rawit & Terong"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap / Lokasi Kebun</label>
                <textarea name="alamat" rows="2" placeholder="Contoh: Dekat Jalan Atilam RT 03 Desa Bajuin"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('alamat') }}</textarea>
            </div>

            <div class="border-b border-slate-100 pb-4 pt-2">
                <h3 class="font-bold text-base text-slate-900">2. Akses Akun Website Petani</h3>
                <p class="text-xs text-slate-500">Petani dapat login ke website untuk memantau status modal & transaksi secara mandiri.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Username Login</label>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Dikosongkan jika ingin dibuat otomatis"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi (Password)</label>
                    <input type="password" name="password" placeholder="Default: password"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                    <span class="text-[11px] text-slate-400 mt-1 block">Jika kosong, password default adalah: <code>password</code></span>
                </div>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.petani.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                    Simpan Data Petani
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

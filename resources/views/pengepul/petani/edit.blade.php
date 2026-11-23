@extends('layouts.app')

@section('title', 'Edit Petani - ' . $petani->nama_petani)
@section('page_title', 'Perbarui Data Petani')
@section('page_subtitle', 'Edit informasi profil dan akun petani ' . $petani->nama_petani)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.petani.update', $petani) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="border-b border-slate-100 pb-4">
                <h3 class="font-bold text-base text-slate-900">Data Profil Petani</h3>
                <p class="text-xs text-slate-500">Kelola informasi mitra tani Desa Bajuin.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap Petani *</label>
                    <input type="text" name="nama_petani" value="{{ old('nama_petani', $petani->nama_petani) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor WhatsApp Aktif *</label>
                    <input type="text" name="no_wa" value="{{ old('no_wa', $petani->no_wa) }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">NIK</label>
                    <input type="text" name="nik" value="{{ old('nik', $petani->nik) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kemitraan *</label>
                    <select name="status" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                        <option value="aktif" {{ $petani->status === 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="nonaktif" {{ $petani->status === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Estimasi Luas Lahan</label>
                    <input type="text" name="luas_lahan" value="{{ old('luas_lahan', $petani->luas_lahan) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Komoditas Utama Sayuran</label>
                    <input type="text" name="komoditas_utama" value="{{ old('komoditas_utama', $petani->komoditas_utama) }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alamat Lengkap Kebun</label>
                <textarea name="alamat" rows="2"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('alamat', $petani->alamat) }}</textarea>
            </div>

            <div class="border-b border-slate-100 pb-4 pt-2">
                <h3 class="font-bold text-base text-slate-900">Perbarui Kata Sandi Akun (Opsional)</h3>
                <p class="text-xs text-slate-500">Username: <strong>{{ $petani->user->username ?? '-' }}</strong>. Kosongkan sandi jika tidak ingin diubah.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
                <input type="password" name="password" placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <button type="button" onclick="if(confirm('Yakin ingin menghapus petani ini?')) { document.getElementById('deleteForm').submit(); }" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold transition">
                    Hapus Petani
                </button>
                <div class="flex space-x-2">
                    <a href="{{ route('pengepul.petani.show', $petani) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="deleteForm" action="{{ route('pengepul.petani.destroy', $petani) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection

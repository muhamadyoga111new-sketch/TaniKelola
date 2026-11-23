@extends('layouts.app')

@section('title', 'Tambah Komoditas')
@section('page_title', 'Tambah Komoditas Baru')
@section('page_subtitle', 'Tambahkan jenis sayuran baru ke sistem pengepul')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <form action="{{ route('pengepul.komoditas.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Komoditas *</label>
                <input type="text" name="nama_komoditas" value="{{ old('nama_komoditas') }}" placeholder="Contoh: Pare Hijau" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori *</label>
                    <input type="text" name="kategori" value="{{ old('kategori', 'Sayuran Buah') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Satuan Timbangan *</label>
                    <input type="text" name="satuan" value="{{ old('satuan', 'kg') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Beli Standar (Rp) *</label>
                <input type="number" name="harga_pasar_default" value="{{ old('harga_pasar_default', 10000) }}" required min="0" step="500"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500 font-bold text-emerald-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Deskripsi</label>
                <textarea name="deskripsi" rows="2" placeholder="Catatan mutu atau varietas"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-emerald-500">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                <a href="{{ route('pengepul.komoditas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 transition">
                    Simpan Komoditas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

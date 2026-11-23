@extends('layouts.app')

@section('title', 'Master Komoditas Sayuran')
@section('page_title', 'Komoditas Sayuran')
@section('page_subtitle', '14 jenis komoditas sayuran yang dibeli oleh pengepul (Batasan Masalah Proposal)')

@section('content')
<div class="space-y-6">

    <!-- Header info banner -->
    <div class="p-5 rounded-3xl bg-emerald-900 text-white shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-base">Daftar Komoditas Budidaya Petani</h3>
            <p class="text-xs text-emerald-200 mt-0.5">
                Harga pasar default digunakan sebagai referensi awal saat penimbangan panen dan dapat disesuaikan pada setiap transaksi.
            </p>
        </div>
        <a href="{{ route('pengepul.komoditas.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-md transition shrink-0">
            <span>+ Tambah Komoditas</span>
        </a>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @foreach($komoditas as $k)
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-5 hover:shadow-md transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            {{ $k->kategori }}
                        </span>
                        <span class="text-xs font-mono text-slate-400">/{{ $k->satuan }}</span>
                    </div>

                    <h4 class="font-bold text-slate-900 text-base mb-1">{{ $k->nama_komoditas }}</h4>
                    <p class="text-xs text-slate-400 mb-4">{{ $k->deskripsi ?? 'Komoditas sayuran mitra' }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-medium">Harga Referensi:</span>
                        <span class="text-sm font-extrabold text-emerald-700">
                            Rp {{ number_format($k->harga_pasar_default, 0, ',', '.') }}
                        </span>
                    </div>
                    <a href="{{ route('pengepul.komoditas.edit', $k) }}" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 text-xs font-semibold text-slate-600 transition">
                        Ubah Harga
                    </a>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

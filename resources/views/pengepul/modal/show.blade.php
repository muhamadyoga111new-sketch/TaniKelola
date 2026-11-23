@extends('layouts.app')

@section('title', 'Bukti Modal ' . $modal->kode_modal)
@section('page_title', 'Rincian Pemberian Modal')
@section('page_subtitle', 'Bukti pencatatan modal budidaya ' . $modal->kode_modal)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs text-slate-400 font-mono font-medium block">NO. BUKTI MODAL</span>
                <h3 class="text-xl font-black text-slate-900">{{ $modal->kode_modal }}</h3>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $modal->jenis_modal === 'barang' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $modal->jenis_modal === 'barang' ? 'Bahan / Barang' : 'Uang Tunai' }}
            </span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Petani Penerima</span>
                <span class="font-bold text-slate-900">{{ $modal->petani->nama_petani }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Tanggal Pemberian</span>
                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($modal->tanggal_pemberian)->translatedFormat('d F Y') }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Kategori Kebutuhan</span>
                <span class="font-semibold text-slate-800">{{ $modal->kategori_kebutuhan }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Rincian Barang / Keperluan</span>
                <span class="font-medium text-slate-700 text-right">{{ $modal->rincian_barang ?? '-' }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Dicatat Oleh</span>
                <span class="font-medium text-slate-700">{{ $modal->user->name ?? 'Pengepul' }}</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                <span class="font-bold text-slate-700 text-sm">Total Nominal Modal:</span>
                <span class="font-black text-slate-900 text-xl">Rp {{ number_format($modal->nominal, 0, ',', '.') }}</span>
            </div>

            @if($modal->catatan)
                <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                    <strong class="block text-slate-800 mb-0.5">Catatan:</strong>
                    {{ $modal->catatan }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <form action="{{ route('pengepul.modal.destroy', $modal) }}" method="POST" onsubmit="return confirm('Hapus catatan modal ini? Kewajiban petani akan otomatis dihitung ulang.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                    Hapus Bukti
                </button>
            </form>

            <a href="{{ route('pengepul.petani.show', $modal->petani) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
                Kembali ke Ledger Petani &rarr;
            </a>
        </div>
    </div>
</div>
@endsection

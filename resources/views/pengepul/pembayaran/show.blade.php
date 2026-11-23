@extends('layouts.app')

@section('title', 'Kwitansi Bayar Tunai ' . $pembayaran->kode_pembayaran)
@section('page_title', 'Bukti Kwitansi Pembayaran Tunai')
@section('page_subtitle', 'Pelunasan / cicilan utang kewajiban ' . $pembayaran->kode_pembayaran)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">

        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <span class="text-xs text-slate-400 font-mono font-medium block">NO. KWITANSI TUNAI</span>
                <h3 class="text-xl font-black text-slate-900">{{ $pembayaran->kode_pembayaran }}</h3>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800">
                Lunas Kas / Tunai
            </span>
        </div>

        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Petani Pembayar</span>
                <span class="font-bold text-slate-900">{{ $pembayaran->petani->nama_petani }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Tanggal Pembayaran</span>
                <span class="font-semibold text-slate-800">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->translatedFormat('d F Y') }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Metode Pembayaran</span>
                <span class="font-semibold text-slate-800">{{ $pembayaran->metode_bayar }}</span>
            </div>

            <div class="flex justify-between py-1 border-b border-slate-50">
                <span class="text-slate-500 text-xs">Diterima Oleh</span>
                <span class="font-medium text-slate-700">{{ $pembayaran->user->name ?? 'Bapak Mohtar' }}</span>
            </div>

            <div class="p-4 rounded-2xl bg-teal-50/70 border border-teal-100 flex items-center justify-between">
                <span class="font-bold text-teal-900 text-sm">Jumlah Pembayaran Tunai:</span>
                <span class="font-black text-teal-800 text-xl">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 block">Sisa Utang Sebelum:</span>
                    <strong class="text-slate-700 font-bold">Rp {{ number_format($pembayaran->sisa_kewajiban_sebelum, 0, ',', '.') }}</strong>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl">
                    <span class="text-slate-400 block">Sisa Utang Terkini:</span>
                    <strong class="text-rose-600 font-bold">Rp {{ number_format($pembayaran->sisa_kewajiban_sesudah, 0, ',', '.') }}</strong>
                </div>
            </div>

            @if($pembayaran->catatan)
                <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                    <strong class="block text-slate-800 mb-0.5">Catatan:</strong>
                    {{ $pembayaran->catatan }}
                </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-4 border-t border-slate-100">
            <form action="{{ route('pengepul.pembayaran.destroy', $pembayaran) }}" method="POST" onsubmit="return confirm('Hapus catatan pembayaran ini? Kewajiban petani akan disesuaikan kembali.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                    Hapus Kwitansi
                </button>
            </form>

            <a href="{{ route('pengepul.petani.show', $pembayaran->petani) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition">
                Kembali ke Ledger Petani &rarr;
            </a>
        </div>
    </div>
</div>
@endsection

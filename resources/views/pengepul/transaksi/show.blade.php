@extends('layouts.app')

@section('title', 'Nota Transaksi ' . $transaksi->nomor_transaksi)
@section('page_title', 'Rincian Transaksi Hasil Panen')
@section('page_subtitle', 'Nota resmi penjualan hasil panen ' . $transaksi->nomor_transaksi)

@section('content')
<style>
    /* HP (<768px): tabel rincian nota berubah jadi kartu per komoditas, supaya
       kolom tidak terpotong dan halaman tidak bisa di-scroll ke samping. */
    @media (max-width: 767px) {
        #notaTable thead { display: none; }
        #notaTable, #notaTable tbody, #notaTable tr, #notaTable td { display: block; width: 100%; }
        #notaTable tbody tr {
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            background: #fff;
        }
        #notaTable td { padding: 0.25rem 0 !important; border: 0; text-align: left !important; }
        #notaTable td::before {
            content: attr(data-label);
            display: block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            margin-bottom: 0.25rem;
        }
        #notaTable td[data-label=""] { display: none; }
        #notaTable td[data-label="Subtotal"] {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            border-top: 1px solid #f1f5f9;
            margin-top: 0.25rem;
            padding-top: 0.5rem !important;
        }
        #notaTable td[data-label="Subtotal"]::before { margin-bottom: 0; }
    }
</style>
@php
    // Kembali ke halaman sebelumnya (daftar modal / detail petani / dashboard, dst).
    // Kalau tidak ada halaman sebelumnya yang aman (misal dibuka langsung,
    // atau habis dari form create), jatuh ke daftar transaksi.
    $previousUrl = url()->previous();
    $backUrl = route('pengepul.transaksi.index');
    if ($previousUrl && $previousUrl !== url()->current() && str_starts_with($previousUrl, url('/')) && !str_contains($previousUrl, '/create') && !str_contains($previousUrl, '/edit')) {
        $backUrl = $previousUrl;
    }
@endphp
<div class="max-w-3xl mx-auto space-y-6">

    <!-- The Invoice Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-6 sm:p-8 space-y-6">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b-2 border-[#eab308] gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 overflow-hidden shrink-0">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo TaniKelola" class="w-full h-full object-cover">
                </div>
                <div>
                    <h3 class="font-black text-xl text-[#14532d] tracking-tight">TaniKelola</h3>
                    <p class="text-xs text-slate-500">Pengepul Sayuran Bajuin &bull; Bpk. Mohtar</p>
                    <p class="text-[11px] text-slate-400">Tanah Laut, Kalimantan Selatan</p>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-[11px] font-mono font-bold uppercase tracking-wider text-slate-400">NOTA TRANSAKSI PANEN</span>
                <div class="text-lg font-black text-slate-900">{{ $transaksi->nomor_transaksi }}</div>
                <div class="text-xs text-slate-500">
                    Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal_transaksi)->translatedFormat('d F Y') }}
                </div>
                <div class="text-xs text-slate-500">
                    Dicatat: {{ $transaksi->created_at->translatedFormat('d F Y, H:i') }} WITA
                </div>
                <div class="mt-1">
                    @if($transaksi->status_wa === 'terkirim')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#14532d]/10 text-[#14532d]">WhatsApp Terkirim</span>
                    @elseif($transaksi->status_wa === 'simulasi')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#eab308]/15 text-[#92400e]">Mode Simulasi WA</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Menunggu WA</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Farmer & Buyer Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-[#f7faf7] border border-slate-200 border-l-4 border-l-[#14532d]">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Petani Penjual:</span>
                <p class="text-sm font-bold text-slate-900">{{ $transaksi->petani->nama_petani }}</p>
                <p class="text-slate-500">WA: {{ $transaksi->petani->no_wa }}</p>
                <p class="text-slate-400 mt-0.5">{{ $transaksi->petani->alamat ?? 'Desa Bajuin' }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-[#f7faf7] border border-slate-200 border-l-4 border-l-[#eab308]">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Pengepul Penerima:</span>
                <p class="text-sm font-bold text-slate-900">Bpk. Mohtar</p>
                <p class="text-slate-500">Pengepul Hasil Tani Bajuin</p>
                <p class="text-slate-400 mt-0.5">Petugas Kasir: {{ $transaksi->user->name ?? 'Admin' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm" id="notaTable">
                <thead class="bg-[#14532d] border-b-2 border-[#eab308] text-[11px] font-bold uppercase tracking-wider text-white">
                    <tr>
                        <th class="py-3 px-4">No.</th>
                        <th class="py-3 px-4">Komoditas Sayuran</th>
                        <th class="py-3 px-4 text-right">Timbangan / Berat</th>
                        <th class="py-3 px-4 text-right">Harga Beli / Satuan</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($transaksi->details as $index => $item)
                        <tr>
                            <td class="py-3 px-4 text-slate-400 font-mono text-xs" data-label="">{{ $index + 1 }}</td>
                            <td class="py-3 px-4" data-label="Komoditas Sayuran">
                                <span class="font-bold text-slate-800 block">{{ $item->komoditas->nama_komoditas }}</span>
                                @if($item->keterangan)
                                    <span class="text-xs text-slate-400">{{ $item->keterangan }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700" data-label="Timbangan / Berat">
                                {{ $item->jumlah_panen }} {{ $item->komoditas->satuan }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700" data-label="Harga Beli / Satuan">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900" data-label="Subtotal">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total and Deductions Breakdown -->
        <div class="border-t border-slate-200 pt-4 flex flex-col sm:flex-row justify-end">
            <div class="w-full sm:w-80 space-y-2 text-sm">
                <div class="flex justify-between py-1 text-slate-600">
                    <span>Total Nilai Panen Bruto:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($transaksi->total_bruto, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between py-1 text-rose-600">
                    <span>Potongan Cicilan Modal:</span>
                    <span class="font-bold">-Rp {{ number_format($transaksi->potongan_kewajiban, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between items-center py-2.5 px-3 rounded-xl bg-[#14532d] border-b-2 border-[#eab308] text-base font-black">
                    <span class="text-white text-sm">UANG DITERIMA PETANI:</span>
                    <span class="text-[#eab308] font-black text-lg">Rp {{ number_format($transaksi->uang_diterima_petani, 0, ',', '.') }}</span>
                </div>

                <div class="pt-2 text-xs text-slate-500 space-y-1">
                    <div class="flex justify-between">
                        <span>Sisa Kewajiban Sebelumnya:</span>
                        <span>Rp {{ number_format($transaksi->sisa_kewajiban_sebelum, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-slate-800">
                        <span>Sisa Kewajiban Petani Terkini:</span>
                        <span class="text-rose-600 font-extrabold">Rp {{ number_format($transaksi->sisa_kewajiban_sesudah, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if($transaksi->catatan)
            <div class="p-3 bg-[#f7faf7] border border-slate-200 rounded-xl text-xs text-slate-600">
                <strong class="text-slate-800">Catatan:</strong> {{ $transaksi->catatan }}
            </div>
        @endif

        <div class="pt-6 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
            <form action="{{ route('pengepul.transaksi.destroy', $transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus transaksi ini? Kewajiban petani akan disesuaikan kembali.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                    Batalkan / Hapus Transaksi Ini
                </button>
            </form>

            <div class="flex flex-wrap items-center gap-2">
                <form action="{{ route('pengepul.transaksi.kirim_wa', $transaksi) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-white hover:bg-[#f7faf7] text-[#14532d] border border-[#14532d]/30 font-bold text-xs transition">
                        Kirim Ulang Notifikasi WhatsApp
                    </button>
                </form>

                <a href="{{ route('pengepul.transaksi.cetak', $transaksi) }}" class="px-4 py-2 rounded-xl bg-[#14532d] hover:bg-[#166534] text-white font-bold text-xs flex items-center space-x-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l-4-4m4 4l4-4M4 21h16"/></svg>
                    <span>Unduh PDF</span>
                </a>

                <a href="{{ route('pengepul.transaksi.cetak', [$transaksi, 'print' => 1, 'autoprint' => 1]) }}" target="_blank" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 font-bold text-xs flex items-center space-x-1.5 transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak</span>
                </a>

                <a href="{{ $backUrl }}" class="px-4 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-200 font-bold text-xs transition">
                    &larr; Kembali
                </a>
            </div>
        </div>
        <p class="text-[11px] text-slate-400 text-right">Dicatat di Sistem TaniKelola</p>

    </div>

</div>
@endsection

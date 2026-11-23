@extends('layouts.app')

@section('title', 'Nota Transaksi ' . $transaksi->nomor_transaksi)
@section('page_title', 'Rincian Transaksi Hasil Panen')
@section('page_subtitle', 'Nota resmi penjualan hasil panen ' . $transaksi->nomor_transaksi)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Action Toolbar -->
    <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <a href="{{ route('pengepul.transaksi.index') }}" class="text-xs font-semibold text-slate-600 hover:text-slate-900 flex items-center space-x-1">
            <span>&larr;</span>
            <span>Kembali ke Daftar</span>
        </a>

        <div class="flex items-center space-x-2">
            <form action="{{ route('pengepul.transaksi.kirim_wa', $transaksi) }}" method="POST">
                @csrf
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-700 font-bold text-xs flex items-center space-x-1.5 transition">
                    <span>💬</span>
                    <span>Kirim Ulang Notifikasi WhatsApp</span>
                </button>
            </form>

            <a href="{{ route('pengepul.transaksi.cetak', $transaksi) }}" target="_blank" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center space-x-1.5 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak Nota (PDF)</span>
            </a>
        </div>
    </div>

    <!-- The Invoice Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">

        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-slate-200/80 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white flex items-center justify-center text-2xl font-black shadow-md">
                    🌱
                </div>
                <div>
                    <h3 class="font-black text-xl text-slate-900 tracking-tight">TaniKelola</h3>
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
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700">WhatsApp Terkirim</span>
                    @elseif($transaksi->status_wa === 'simulasi')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Mode Simulasi WA</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-700">Pending WA</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Farmer & Buyer Info -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Petani Penjual:</span>
                <p class="text-sm font-bold text-slate-900">{{ $transaksi->petani->nama_petani }}</p>
                <p class="text-slate-500">WA: {{ $transaksi->petani->no_wa }}</p>
                <p class="text-slate-400 mt-0.5">{{ $transaksi->petani->alamat ?? 'Desa Bajuin' }}</p>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                <span class="font-bold text-slate-400 uppercase tracking-wider block mb-1">Pengepul Penerima:</span>
                <p class="text-sm font-bold text-slate-900">Bpk. Mohtar</p>
                <p class="text-slate-500">Pengepul Hasil Tani Bajuin</p>
                <p class="text-slate-400 mt-0.5">Petugas Kasir: {{ $transaksi->user->name ?? 'Admin' }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-y border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
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
                            <td class="py-3 px-4 text-slate-400 font-mono text-xs">{{ $index + 1 }}</td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-800 block">{{ $item->komoditas->nama_komoditas }}</span>
                                @if($item->keterangan)
                                    <span class="text-xs text-slate-400">{{ $item->keterangan }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700">
                                {{ $item->jumlah_panen }} {{ $item->komoditas->satuan }}
                            </td>
                            <td class="py-3 px-4 text-right font-medium text-slate-700">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Total and Deductions Breakdown -->
        <div class="border-t border-slate-200/80 pt-4 flex flex-col sm:flex-row justify-end">
            <div class="w-full sm:w-80 space-y-2 text-sm">
                <div class="flex justify-between py-1 text-slate-600">
                    <span>Total Nilai Panen Bruto:</span>
                    <span class="font-bold text-slate-900">Rp {{ number_format($transaksi->total_bruto, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between py-1 text-rose-600">
                    <span>Potongan Cicilan Modal:</span>
                    <span class="font-bold">-Rp {{ number_format($transaksi->potongan_kewajiban, 0, ',', '.') }}</span>
                </div>

                <div class="flex justify-between py-2 border-y border-slate-200 text-base font-black text-emerald-800 bg-emerald-50/50 px-3 rounded-xl">
                    <span>UANG DITERIMA PETANI:</span>
                    <span class="text-emerald-700 font-black text-lg">Rp {{ number_format($transaksi->uang_diterima_petani, 0, ',', '.') }}</span>
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
            <div class="p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                <strong class="text-slate-800">Catatan:</strong> {{ $transaksi->catatan }}
            </div>
        @endif

        <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
            <form action="{{ route('pengepul.transaksi.destroy', $transaksi) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan dan menghapus transaksi ini? Kewajiban petani akan disesuaikan kembali.');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs font-bold text-rose-600 hover:text-rose-700">
                    Batalkan / Hapus Transaksi Ini
                </button>
            </form>

            <span class="text-[11px] text-slate-400">Dicatat di Sistem TaniKelola</span>
        </div>

    </div>

</div>
@endsection

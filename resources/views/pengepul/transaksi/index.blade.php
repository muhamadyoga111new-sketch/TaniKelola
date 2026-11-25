@extends('layouts.app')

@section('title', 'Transaksi Panen')
@section('page_title', 'Transaksi Panen')
@section('page_subtitle', 'Pencatatan penjualan hasil panen sayuran')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Filter and Add Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <form method="GET" action="{{ route('pengepul.transaksi.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mr-1">Saring</span>
            <select name="petani_id" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#15803d] focus:border-transparent">
                <option value="">-- Semua Petani --</option>
                @foreach($petanis as $p)
                    <option value="{{ $p->id }}" {{ $petaniId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_petani }}
                    </option>
                @endforeach
            </select>

            <div class="flex items-center space-x-1 text-xs text-slate-500">
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-2 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#15803d] focus:border-transparent">
                <span>-</span>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-2 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-[#15803d] focus:border-transparent">
            </div>

            <button type="submit" class="px-4 py-2 rounded-xl bg-[#14532d] text-white font-semibold text-xs hover:bg-[#166534] transition">
                Filter
            </button>
        </form>

        <a href="{{ route('pengepul.transaksi.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-[#eab308] hover:bg-[#facc15] text-[#1a2e22] font-bold text-xs shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Catat Transaksi Baru</span>
        </a>
    </div>

    <!-- Summary Ledger -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs grid grid-cols-1 sm:grid-cols-3 divide-y sm:divide-y-0 sm:divide-x divide-slate-100">
        <div class="p-4 border-l-4 border-[#14532d]">
            <span class="text-[10px] sm:text-xs text-slate-500 font-bold uppercase tracking-wider">Total Nilai Panen</span>
            <div class="text-base sm:text-lg font-extrabold text-slate-900 mt-0.5">Rp {{ number_format($totalNilaiPanen, 0, ',', '.') }}</div>
        </div>
        <div class="p-4 border-l-4 border-rose-500">
            <span class="text-[10px] sm:text-xs text-rose-600 font-bold uppercase tracking-wider">Potongan Modal</span>
            <div class="text-base sm:text-lg font-extrabold text-rose-600 mt-0.5">Rp {{ number_format($totalPotonganUtang, 0, ',', '.') }}</div>
        </div>
        <div class="p-4 border-l-4 border-[#eab308]">
            <span class="text-[10px] sm:text-xs text-[#14532d] font-bold uppercase tracking-wider">Uang Diterima Petani</span>
            <div class="text-base sm:text-lg font-extrabold text-[#14532d] mt-0.5">Rp {{ number_format($totalUangDiterima, 0, ',', '.') }}</div>
        </div>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-[#14532d] border-b-2 border-[#eab308] text-[11px] font-bold uppercase tracking-wider text-white">
                    <tr>
                        <th class="py-3 px-4">Transaksi</th>
                        <th class="py-3 px-4">Petani</th>
                        <th class="py-3 px-4">Komoditas</th>
                        <th class="py-3 px-4 text-right">Nilai</th>
                        <th class="py-3 px-4 text-right">Potong</th>
                        <th class="py-3 px-4 text-right">Diterima</th>
                        <th class="py-3 px-4 text-center">WA</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksis as $t)
                        <tr class="hover:bg-[#f7faf7] transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('pengepul.transaksi.show', $t) }}" class="text-[#14532d] hover:underline font-semibold text-xs">
                                    {{ $t->nomor_transaksi }}
                                </a>
                                <span class="block text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <span class="font-bold text-slate-800">{{ $t->petani->nama_petani }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">
                                @foreach($t->details as $d)
                                    <span class="inline-block bg-[#f7faf7] border border-slate-200 px-1.5 py-0.5 rounded text-[10px] font-medium mr-0.5 mb-0.5">
                                        {{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-right text-xs font-semibold text-slate-800">
                                Rp {{ number_format($t->total_bruto, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-xs font-bold text-rose-600">
                                -{{ number_format($t->potongan_kewajiban, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-xs font-bold text-[#14532d]">
                                Rp {{ number_format($t->uang_diterima_petani, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($t->status_wa === 'terkirim')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#14532d]/10 text-[#14532d]">Terkirim</span>
                                @elseif($t->status_wa === 'simulasi')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#eab308]/15 text-[#92400e]">Simulasi</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Pending</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('pengepul.transaksi.show', $t) }}" class="p-1.5 rounded-lg bg-[#14532d]/10 hover:bg-[#14532d]/20 text-[#14532d] text-xs font-semibold">Detail</a>
                                    <a href="{{ route('pengepul.transaksi.cetak', $t) }}" target="_blank" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Nota</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 text-sm">
                                Belum ada transaksi panen.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse($transaksis as $t)
            <div class="bg-white rounded-xl border border-slate-200 border-l-4 border-l-[#14532d] shadow-xs p-4">
                <div class="flex items-center justify-between mb-2">
                    <a href="{{ route('pengepul.transaksi.show', $t) }}" class="text-xs font-bold text-[#14532d] hover:underline">{{ $t->nomor_transaksi }}</a>
                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($t->tanggal_transaksi)->translatedFormat('d M Y') }}</span>
                </div>
                <p class="text-xs font-medium text-slate-800">{{ $t->petani->nama_petani }}</p>
                <div class="flex flex-wrap gap-1 mt-1.5">
                    @foreach($t->details as $d)
                        <span class="bg-[#f7faf7] border border-slate-200 px-1.5 py-0.5 rounded text-[10px] font-medium text-slate-600">{{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }}</span>
                    @endforeach
                </div>
                <div class="flex items-center justify-between mt-3 pt-2 border-t border-slate-100">
                    <div class="text-[10px] text-slate-500">
                        Bruto: <span class="font-semibold">Rp {{ number_format($t->total_bruto, 0, ',', '.') }}</span>
                        <span class="text-rose-500 ml-1">-{{ number_format($t->potongan_kewajiban, 0, ',', '.') }}</span>
                    </div>
                    <span class="text-xs font-bold text-[#14532d]">Rp {{ number_format($t->uang_diterima_petani, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-center justify-between mt-2">
                    @if($t->status_wa === 'terkirim')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#14532d]/10 text-[#14532d]">WA Terkirim</span>
                    @elseif($t->status_wa === 'simulasi')
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#eab308]/15 text-[#92400e]">Simulasi</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Pending</span>
                    @endif
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('pengepul.transaksi.show', $t) }}" class="text-xs font-semibold text-[#14532d]">Detail</a>
                        <a href="{{ route('pengepul.transaksi.cetak', $t) }}" target="_blank" class="text-xs font-semibold text-slate-500">Nota</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200">
                Belum ada transaksi panen.
            </div>
        @endforelse

        @if($transaksis->hasPages())
            <div class="p-3">
                {{ $transaksis->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

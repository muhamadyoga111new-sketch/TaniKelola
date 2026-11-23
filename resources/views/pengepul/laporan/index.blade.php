@extends('layouts.app')

@section('title', 'Laporan & Rekap')
@section('page_title', 'Laporan & Rekap')
@section('page_subtitle', 'Rekapitulasi kewajiban, penjualan, dan pemberian modal')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white p-4 sm:p-6 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('pengepul.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Jenis Laporan *</label>
                <select name="jenis" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500 font-medium">
                    <option value="kewajiban" {{ $jenisLaporan === 'kewajiban' ? 'selected' : '' }}>📑 Rekap Kewajiban & Piutang Petani</option>
                    <option value="transaksi" {{ $jenisLaporan === 'transaksi' ? 'selected' : '' }}>🌾 Rekap Transaksi Penjualan Panen</option>
                    <option value="modal" {{ $jenisLaporan === 'modal' ? 'selected' : '' }}>💰 Rekap Pemberian Modal Tani</option>
                </select>
            </div>

            @if($jenisLaporan !== 'kewajiban')
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                </div>
            @else
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Filter Petani Spesifik</label>
                    <select name="petani_id" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                        <option value="">-- Semua Petani Mitra --</option>
                        @foreach($petanis as $p)
                            <option value="{{ $p->id }}" {{ $petaniId == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_petani }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <div class="flex space-x-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Tampilkan Data
                </button>
                <a href="{{ route('pengepul.laporan.cetak', request()->query()) }}" target="_blank" class="py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center justify-center space-x-1 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak PDF</span>
                </a>
            </div>
        </form>
    </div>

    <!-- Report Table Content -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-extrabold text-base text-slate-900">
                    @if($jenisLaporan === 'kewajiban')
                        Laporan Rekapitulasi Kewajiban & Piutang Petani
                    @elseif($jenisLaporan === 'transaksi')
                        Laporan Rekapitulasi Penjualan Panen (Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                    @else
                        Laporan Rekapitulasi Pemberian Modal Tani (Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }})
                    @endif
                </h3>
                <p class="text-xs text-slate-500 hidden sm:block">Data petani mitra aktif</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            @if($jenisLaporan === 'kewajiban')
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">No.</th>
                            <th class="py-3 px-6">Petani Mitra</th>
                            <th class="py-3 px-6">No. WhatsApp</th>
                            <th class="py-3 px-6 text-right">Total Modal Diterima</th>
                            <th class="py-3 px-6 text-right">Potongan Panen</th>
                            <th class="py-3 px-6 text-right">Bayar Tunai</th>
                            <th class="py-3 px-6 text-right">Total Terbayar</th>
                            <th class="py-3 px-6 text-right">Sisa Kewajiban</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $sumModal = 0;
                            $sumPotongan = 0;
                            $sumTunai = 0;
                            $sumSisa = 0;
                        @endphp
                        @forelse($dataLaporan as $i => $row)
                            @php
                                $potonganPanen = $row->transaksiPanens->sum('potongan_kewajiban');
                                $bayarTunai = $row->pembayaranKewajibans->sum('jumlah_bayar');
                                $sumModal += $row->total_modal;
                                $sumPotongan += $potonganPanen;
                                $sumTunai += $bayarTunai;
                                $sumSisa += $row->sisa_kewajiban;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-6 font-mono text-slate-400 text-xs">{{ $i + 1 }}</td>
                                <td class="py-3.5 px-6 font-bold text-slate-800">
                                    {{ $row->nama_petani }}
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-600">{{ $row->no_wa }}</td>
                                <td class="py-3.5 px-6 text-right font-medium">Rp {{ number_format($row->total_modal, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-medium text-emerald-700">Rp {{ number_format($potonganPanen, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-medium text-teal-700">Rp {{ number_format($bayarTunai, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-slate-800">Rp {{ number_format($row->total_terbayar, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-black {{ $row->sisa_kewajiban > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    Rp {{ number_format($row->sisa_kewajiban, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="py-8 text-center text-slate-400 text-sm">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-xs border-t-2 border-slate-300">
                        <tr>
                            <td colspan="3" class="py-3 px-6 text-slate-700">TOTAL KESELURUHAN:</td>
                            <td class="py-3 px-6 text-right text-slate-900">Rp {{ number_format($sumModal, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-emerald-800">Rp {{ number_format($sumPotongan, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-teal-800">Rp {{ number_format($sumTunai, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-slate-900">Rp {{ number_format($sumPotongan + $sumTunai, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-rose-700 font-black">Rp {{ number_format($sumSisa, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @elseif($jenisLaporan === 'transaksi')
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">No. Transaksi & Tanggal</th>
                            <th class="py-3 px-6">Petani</th>
                            <th class="py-3 px-6">Rincian Komoditas</th>
                            <th class="py-3 px-6 text-right">Nilai Bruto</th>
                            <th class="py-3 px-6 text-right">Potongan Utang</th>
                            <th class="py-3 px-6 text-right">Uang Diterima Petani</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php
                            $sumBruto = 0;
                            $sumPotong = 0;
                            $sumBersih = 0;
                        @endphp
                        @forelse($dataLaporan as $trx)
                            @php
                                $sumBruto += $trx->total_bruto;
                                $sumPotong += $trx->potongan_kewajiban;
                                $sumBersih += $trx->uang_diterima_petani;
                            @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    {{ $trx->nomor_transaksi }}
                                    <span class="block text-xs text-slate-400 font-normal">{{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-800">{{ $trx->petani->nama_petani }}</td>
                                <td class="py-3.5 px-6 text-xs text-slate-600">
                                    @foreach($trx->details as $d)
                                        <div>{{ $d->komoditas->nama_komoditas }}: {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }} @ Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</div>
                                    @endforeach
                                </td>
                                <td class="py-3.5 px-6 text-right font-medium">Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-bold text-rose-600">-Rp {{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right font-black text-emerald-700">Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="py-8 text-center text-slate-400 text-sm">Tidak ada transaksi panen pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-xs border-t-2 border-slate-300">
                        <tr>
                            <td colspan="3" class="py-3 px-6 text-slate-700">TOTAL:</td>
                            <td class="py-3 px-6 text-right text-slate-900">Rp {{ number_format($sumBruto, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-rose-700">-Rp {{ number_format($sumPotong, 0, ',', '.') }}</td>
                            <td class="py-3 px-6 text-right text-emerald-800 font-black">Rp {{ number_format($sumBersih, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-6">Kode & Tanggal</th>
                            <th class="py-3 px-6">Petani Penerima</th>
                            <th class="py-3 px-6">Bentuk & Kategori</th>
                            <th class="py-3 px-6">Rincian Barang / Keperluan</th>
                            <th class="py-3 px-6 text-right">Nominal Modal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @php $sumModalLap = 0; @endphp
                        @forelse($dataLaporan as $m)
                            @php $sumModalLap += $m->nominal; @endphp
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3.5 px-6 font-semibold text-slate-900">
                                    {{ $m->kode_modal }}
                                    <span class="block text-xs text-slate-400 font-normal">{{ \Carbon\Carbon::parse($m->tanggal_pemberian)->translatedFormat('d M Y') }}</span>
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-800">{{ $m->petani->nama_petani }}</td>
                                <td class="py-3.5 px-6 text-xs">
                                    <span class="font-bold block">{{ ucfirst($m->jenis_modal) }}</span>
                                    <span class="text-slate-500">{{ $m->kategori_kebutuhan }}</span>
                                </td>
                                <td class="py-3.5 px-6 text-xs text-slate-600">{{ $m->rincian_barang ?? '-' }}</td>
                                <td class="py-3.5 px-6 text-right font-extrabold text-slate-900">Rp {{ number_format($m->nominal, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="py-8 text-center text-slate-400 text-sm">Tidak ada data pemberian modal pada rentang tanggal ini.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-slate-100 font-bold text-xs border-t-2 border-slate-300">
                        <tr>
                            <td colspan="4" class="py-3 px-6 text-slate-700">TOTAL MODAL DIBERIKAN:</td>
                            <td class="py-3 px-6 text-right text-slate-900 font-black">Rp {{ number_format($sumModalLap, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </div>
    </div>

</div>
@endsection

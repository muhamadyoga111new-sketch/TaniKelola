@extends('layouts.app')

@section('title', 'Ledger Petani - ' . $petani->nama_petani)
@section('page_title', 'Buku Kendali & Ledger Petani')
@section('page_subtitle', $petani->nama_petani . ' - Desa Bajuin, Tanah Laut')

@section('content')
<div class="space-y-6">

    <!-- Top Petani Profile Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-start space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white flex items-center justify-center font-extrabold text-2xl shadow-lg shadow-emerald-600/20 shrink-0">
                {{ substr($petani->nama_petani, 0, 1) }}
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <h3 class="text-xl font-black text-slate-900">{{ $petani->nama_petani }}</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $petani->status === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ strtoupper($petani->status) }}
                    </span>
                </div>
                <div class="text-xs text-slate-500 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                    <span>No. WA: <strong class="text-emerald-700">{{ $petani->no_wa }}</strong></span>
                    <span>Lahan: <strong>{{ $petani->luas_lahan ?? '-' }}</strong></span>
                    <span>Fokus: <strong>{{ $petani->komoditas_utama ?? '-' }}</strong></span>
                </div>
                <p class="text-xs text-slate-400 mt-1">Alamat: {{ $petani->alamat ?? 'Desa Bajuin' }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $petani->no_wa) }}" target="_blank" class="px-3.5 py-2 rounded-xl bg-green-50 hover:bg-green-100 text-green-800 font-bold text-xs flex items-center space-x-1.5 transition">
                <span>💬</span>
                <span>Chat WhatsApp</span>
            </a>
            <a href="{{ route('pengepul.transaksi.create', ['petani_id' => $petani->id]) }}" class="px-3.5 py-2 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-bold text-xs shadow-sm transition">
                + Catat Panen
            </a>
            <a href="{{ route('pengepul.modal.create', ['petani_id' => $petani->id]) }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition">
                + Beri Modal
            </a>
            <a href="{{ route('pengepul.pembayaran.create', ['petani_id' => $petani->id]) }}" class="px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-sm transition">
                + Bayar Tunai
            </a>
        </div>
    </div>

    <!-- 4 Summary Stat Cards for this Petani -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Modal Tani</span>
            <div class="text-xl font-extrabold text-slate-900 mt-1">
                Rp {{ number_format($petani->total_modal, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $petani->modals->count() }} kali pemberian modal</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider block">Potongan dari Panen</span>
            <div class="text-xl font-extrabold text-emerald-700 mt-1">
                Rp {{ number_format($petani->transaksiPanens->sum('potongan_kewajiban'), 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400 mt-0.5 block">Dari {{ $petani->transaksiPanens->count() }} transaksi panen</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-teal-600 uppercase tracking-wider block">Pembayaran Tunai Kas</span>
            <div class="text-xl font-extrabold text-teal-700 mt-1">
                Rp {{ number_format($petani->pembayaranKewajibans->sum('jumlah_bayar'), 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-slate-400 mt-0.5 block">{{ $petani->pembayaranKewajibans->count() }} kali bayar langsung</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-rose-200 shadow-xs bg-gradient-to-br from-white to-rose-50/40">
            <span class="text-xs font-semibold text-rose-700 uppercase tracking-wider block">Sisa Kewajiban Saat Ini</span>
            <div class="text-2xl font-black text-rose-600 mt-1">
                Rp {{ number_format($petani->sisa_kewajiban, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-rose-500 mt-0.5 block font-medium">
                {{ $petani->sisa_kewajiban == 0 ? 'Kewajiban telah LUNAS' : 'Wajib diselesaikan dari panen / kas' }}
            </span>
        </div>
    </div>

    <!-- History Tables with Tabs -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden" x-data="{ tab: 'panen' }">
        <!-- Tabs Header -->
        <div class="flex border-b border-slate-200 bg-slate-50/50 px-6 pt-3">
            <button type="button" @click="tab = 'panen'" :class="tab === 'panen' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl">
                🌾 Riwayat Penjualan Hasil Panen ({{ $petani->transaksiPanens->count() }})
            </button>
            <button type="button" @click="tab = 'modal'" :class="tab === 'modal' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl">
                💰 Riwayat Pemberian Modal ({{ $petani->modals->count() }})
            </button>
            <button type="button" @click="tab = 'tunai'" :class="tab === 'tunai' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl">
                💵 Riwayat Bayar Tunai ({{ $petani->pembayaranKewajibans->count() }})
            </button>
        </div>

        <!-- Tab 1: Transaksi Panen -->
        <div x-show="tab === 'panen'" class="p-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-2.5">No. Transaksi & Tanggal</th>
                            <th class="py-2.5">Rincian Komoditas & Timbangan</th>
                            <th class="py-2.5 text-right">Nilai Bruto</th>
                            <th class="py-2.5 text-right">Potong Utang</th>
                            <th class="py-2.5 text-right">Uang Bersih Diterima</th>
                            <th class="py-2.5 text-right">Sisa Utang Akhir</th>
                            <th class="py-2.5 text-center">Nota</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($petani->transaksiPanens as $trx)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 font-semibold text-slate-900">
                                    <a href="{{ route('pengepul.transaksi.show', $trx) }}" class="text-emerald-700 hover:underline">
                                        {{ $trx->nomor_transaksi }}
                                    </a>
                                    <span class="block text-xs text-slate-400 font-normal">
                                        {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="py-3 text-xs text-slate-600">
                                    @foreach($trx->details as $d)
                                        <div class="py-0.5">
                                            <strong>{{ $d->komoditas->nama_komoditas }}</strong>: 
                                            {{ $d->jumlah_panen }} {{ $d->komoditas->satuan }} 
                                            @ Rp {{ number_format($d->harga_beli, 0, ',', '.') }} = 
                                            <span class="font-medium text-slate-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                <td class="py-3 text-right font-medium text-slate-800">
                                    Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-bold text-rose-600">
                                    -Rp {{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-black text-emerald-700">
                                    Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-medium text-slate-600">
                                    Rp {{ number_format($trx->sisa_kewajiban_sesudah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-center">
                                    <a href="{{ route('pengepul.transaksi.cetak', $trx) }}" target="_blank" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-xs font-semibold text-slate-700">
                                        Cetak
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada transaksi hasil panen untuk petani ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 2: Riwayat Modal -->
        <div x-show="tab === 'modal'" class="p-6" style="display:none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-2.5">Kode & Tanggal</th>
                            <th class="py-2.5">Jenis Modal</th>
                            <th class="py-2.5">Kebutuhan</th>
                            <th class="py-2.5">Rincian Barang / Keperluan</th>
                            <th class="py-2.5 text-right">Nominal Modal</th>
                            <th class="py-2.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($petani->modals as $m)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 font-semibold text-slate-900">
                                    {{ $m->kode_modal }}
                                    <span class="block text-xs text-slate-400 font-normal">
                                        {{ \Carbon\Carbon::parse($m->tanggal_pemberian)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-bold {{ $m->jenis_modal === 'barang' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                        {{ ucfirst($m->jenis_modal) }}
                                    </span>
                                </td>
                                <td class="py-3 font-medium text-slate-800 text-xs">
                                    {{ $m->kategori_kebutuhan }}
                                </td>
                                <td class="py-3 text-xs text-slate-600">
                                    {{ $m->rincian_barang ?? '-' }}
                                </td>
                                <td class="py-3 text-right font-extrabold text-slate-900">
                                    Rp {{ number_format($m->nominal, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-xs text-slate-400">
                                    {{ $m->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada data pemberian modal.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab 3: Riwayat Pembayaran Tunai -->
        <div x-show="tab === 'tunai'" class="p-6" style="display:none;">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <tr>
                            <th class="py-2.5">No. Kwitansi & Tanggal</th>
                            <th class="py-2.5">Metode Bayar</th>
                            <th class="py-2.5 text-right">Jumlah Bayar Tunai</th>
                            <th class="py-2.5 text-right">Sisa Kewajiban Akhir</th>
                            <th class="py-2.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($petani->pembayaranKewajibans as $b)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td class="py-3 font-semibold text-slate-900">
                                    {{ $b->kode_pembayaran }}
                                    <span class="block text-xs text-slate-400 font-normal">
                                        {{ \Carbon\Carbon::parse($b->tanggal_bayar)->translatedFormat('d M Y') }}
                                    </span>
                                </td>
                                <td class="py-3 text-xs font-medium text-slate-700">
                                    {{ $b->metode_bayar }}
                                </td>
                                <td class="py-3 text-right font-black text-teal-700">
                                    Rp {{ number_format($b->jumlah_bayar, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-right font-medium text-slate-600">
                                    Rp {{ number_format($b->sisa_kewajiban_sesudah, 0, ',', '.') }}
                                </td>
                                <td class="py-3 text-xs text-slate-400">
                                    {{ $b->catatan ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400 text-xs">
                                    Belum ada catatan pembayaran tunai langsung di luar panen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Alpine JS for Tabs -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

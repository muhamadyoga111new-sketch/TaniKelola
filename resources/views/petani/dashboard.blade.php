@extends('layouts.app')

@section('title', 'Portal Petani - ' . $petani->nama_petani)
@section('page_title', 'Status & Riwayat Saya')
@section('page_subtitle', 'Buku kendali modal budidaya dan penjualan panen sayuran Anda')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Farmer Welcome Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-emerald-800 via-teal-800 to-emerald-950 text-white shadow-xl relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-200 border border-emerald-400/30 mb-2">
                    🌾 Mitra Petani Desa Bajuin
                </span>
                <h3 class="text-2xl font-black tracking-tight">Halo, {{ $petani->nama_petani }}!</h3>
                <p class="text-xs text-emerald-200/90 mt-1">
                    Kode Petani: <strong class="font-mono">{{ $petani->kode_petani }}</strong> &bull; Lahan: {{ $petani->luas_lahan ?? 'Bajuin' }} &bull; Komoditas: {{ $petani->komoditas_utama ?? 'Sayuran' }}
                </p>
            </div>

            <!-- Quick WhatsApp Hubungi Pengepul -->
            @if($pengepul)
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $pengepul->phone ?? '081234567890') }}?text=Halo%20Bapak%20Mohtar,%20saya%20{{ urlencode($petani->nama_petani) }}%20ingin%20bertanya%20mengenai%20transaksi/modal%20tani"
                    target="_blank"
                    class="inline-flex items-center space-x-2 px-4 py-3 rounded-2xl bg-white text-emerald-950 font-extrabold text-xs shadow-md hover:bg-emerald-50 transition shrink-0">
                    <span>💬</span>
                    <span>Hubungi Pengepul (Bpk. Mohtar)</span>
                </a>
            @endif
        </div>
    </div>

    <!-- 4 Stats Cards for the Farmer -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Sisa Kewajiban Utama -->
        <div class="bg-white p-5 rounded-3xl border-2 {{ $petani->sisa_kewajiban > 0 ? 'border-rose-200 bg-rose-50/20' : 'border-emerald-200 bg-emerald-50/20' }} shadow-xs">
            <span class="text-xs font-bold uppercase tracking-wider {{ $petani->sisa_kewajiban > 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                Sisa Kewajiban / Utang Modal
            </span>
            <div class="text-2xl font-black mt-2 {{ $petani->sisa_kewajiban > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                Rp {{ number_format($petani->sisa_kewajiban, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-500 mt-1">
                {{ $petani->sisa_kewajiban > 0 ? 'Dipotong dari hasil panen berikutnya atau bayar tunai' : 'Alhamdulillah kewajiban telah lunas ✨' }}
            </p>
        </div>

        <!-- 2. Total Modal Pernah Diterima -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Modal Diterima</span>
            <div class="text-xl font-extrabold text-slate-900 mt-2">
                Rp {{ number_format($petani->total_modal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ $petani->modals->count() }} kali bantuan budidaya</p>
        </div>

        <!-- 3. Total Cicilan Terbayar -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-emerald-600 uppercase tracking-wider">Total Telah Dicicil/Lunas</span>
            <div class="text-xl font-extrabold text-emerald-700 mt-2">
                Rp {{ number_format($petani->total_terbayar, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Via panen & tunai</p>
        </div>

        <!-- 4. Total Uang Bersih Panen -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-amber-600 uppercase tracking-wider">Uang Panen Diterima</span>
            <div class="text-xl font-extrabold text-amber-700 mt-2">
                Rp {{ number_format($petani->transaksiPanens->sum('uang_diterima_petani'), 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Dari {{ $petani->transaksiPanens->count() }} kali setor panen</p>
        </div>
    </div>

    <!-- History Tables with Tabs -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden" x-data="{ activeTab: 'panen' }">
        <div class="flex border-b border-slate-200 bg-slate-50/50 px-6 pt-3 overflow-x-auto">
            <button type="button" @click="activeTab = 'panen'" :class="activeTab === 'panen' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl shrink-0">
                🌾 Riwayat Setor Panen & Pemotongan ({{ $petani->transaksiPanens->count() }})
            </button>
            <button type="button" @click="activeTab = 'modal'" :class="activeTab === 'modal' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl shrink-0">
                💰 Bantuan Modal dari Pengepul ({{ $petani->modals->count() }})
            </button>
            <button type="button" @click="activeTab = 'tunai'" :class="activeTab === 'tunai' ? 'border-emerald-600 text-emerald-800 font-bold bg-white' : 'border-transparent text-slate-500 hover:text-slate-700'" class="px-5 py-3 border-b-2 text-xs transition rounded-t-xl shrink-0">
                💵 Pembayaran Tunai Kas ({{ $petani->pembayaranKewajibans->count() }})
            </button>
        </div>

        <!-- Tab 1: Riwayat Panen -->
        <div x-show="activeTab === 'panen'" class="p-6">
            <div class="space-y-4">
                @forelse($petani->transaksiPanens as $trx)
                    <div class="p-5 rounded-2xl border border-slate-200/80 hover:border-emerald-300 transition bg-slate-50/30">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-200/60 gap-2">
                            <div>
                                <span class="font-mono text-xs font-bold text-slate-800">{{ $trx->nomor_transaksi }}</span>
                                <span class="text-xs text-slate-400 ml-2">&bull; {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('l, d F Y') }}</span>
                            </div>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold {{ $trx->status_wa === 'terkirim' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
                                Nota WA: {{ ucfirst($trx->status_wa) }}
                            </span>
                        </div>

                        <!-- Commodity Details -->
                        <div class="py-3">
                            <span class="text-xs font-bold text-slate-500 uppercase block mb-1.5">Hasil Panen Disetor:</span>
                            <div class="space-y-1">
                                @foreach($trx->details as $d)
                                    <div class="flex justify-between items-center text-xs">
                                        <span class="text-slate-700 font-medium">
                                            &bull; {{ $d->komoditas->nama_komoditas }} ({{ $d->jumlah_panen }} {{ $d->komoditas->satuan }})
                                            <span class="text-slate-400 font-normal">@ Rp {{ number_format($d->harga_beli, 0, ',', '.') }}</span>
                                        </span>
                                        <strong class="text-slate-800">Rp {{ number_format($d->subtotal, 0, ',', '.') }}</strong>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Financial Ledger Calculation -->
                        <div class="pt-3 border-t border-slate-200/60 flex flex-col sm:flex-row sm:items-center justify-between text-xs gap-2">
                            <div class="space-x-3 text-slate-600">
                                <span>Nilai Panen: <strong>Rp {{ number_format($trx->total_bruto, 0, ',', '.') }}</strong></span>
                                <span class="text-rose-600">Potong Utang: <strong>-Rp {{ number_format($trx->potongan_kewajiban, 0, ',', '.') }}</strong></span>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="text-slate-500">Uang Diterima:</span>
                                <strong class="text-emerald-700 text-sm font-black ml-1">Rp {{ number_format($trx->uang_diterima_petani, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-sm">
                        Belum ada riwayat transaksi penjualan hasil panen.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 2: Modal Diterima -->
        <div x-show="activeTab === 'modal'" class="p-6" style="display:none;">
            <div class="space-y-3">
                @forelse($petani->modals as $m)
                    <div class="p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="font-bold text-sm text-slate-900">{{ $m->kategori_kebutuhan }}</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $m->jenis_modal === 'barang' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                                    {{ ucfirst($m->jenis_modal) }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $m->rincian_barang ?? 'Kebutuhan budidaya' }}</p>
                            <span class="text-[11px] text-slate-400 block mt-1 font-mono">
                                {{ $m->kode_modal }} &bull; {{ \Carbon\Carbon::parse($m->tanggal_pemberian)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-[11px] text-slate-400 block">Nominal Modal:</span>
                            <span class="text-base font-black text-slate-900">Rp {{ number_format($m->nominal, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-sm">
                        Belum ada riwayat bantuan modal yang dicatat.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab 3: Pembayaran Tunai -->
        <div x-show="activeTab === 'tunai'" class="p-6" style="display:none;">
            <div class="space-y-3">
                @forelse($petani->pembayaranKewajibans as $b)
                    <div class="p-4 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div>
                            <span class="font-bold text-sm text-slate-900">Pembayaran Kas: {{ $b->kode_pembayaran }}</span>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $b->catatan ?? 'Pembayaran langsung uang tunai' }}</p>
                            <span class="text-[11px] text-slate-400 block mt-1">
                                {{ \Carbon\Carbon::parse($b->tanggal_bayar)->translatedFormat('d F Y') }} &bull; Metode: {{ $b->metode_bayar }}
                            </span>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="text-[11px] text-teal-600 block font-semibold">Telah Dibayarkan:</span>
                            <span class="text-base font-black text-teal-700">Rp {{ number_format($b->jumlah_bayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 text-sm">
                        Belum ada riwayat pembayaran tunai langsung di luar panen.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>

<!-- Alpine.js -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

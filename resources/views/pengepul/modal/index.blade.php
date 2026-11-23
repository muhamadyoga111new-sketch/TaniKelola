@extends('layouts.app')

@section('title', 'Pemberian Modal')
@section('page_title', 'Pemberian Modal')
@section('page_subtitle', 'Pencatatan modal kebutuhan budidaya ke petani')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Top Filter & Add Bar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs space-y-3 sm:space-y-0 sm:flex sm:flex-wrap sm:items-center sm:justify-between sm:gap-4">
        <form method="GET" action="{{ route('pengepul.modal.index') }}" class="flex flex-wrap items-center gap-2 sm:gap-3">
            <select name="petani_id" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Semua Petani --</option>
                @foreach($petanis as $p)
                    <option value="{{ $p->id }}" {{ $petaniId == $p->id ? 'selected' : '' }}>
                        {{ $p->nama_petani }}
                    </option>
                @endforeach
            </select>

            <select name="jenis_modal" class="w-full sm:w-auto px-3 py-2 rounded-xl border border-slate-200 text-xs focus:ring-2 focus:ring-emerald-500">
                <option value="">-- Semua Jenis --</option>
                <option value="barang" {{ $jenisModal === 'barang' ? 'selected' : '' }}>Barang / Bahan</option>
                <option value="uang" {{ $jenisModal === 'uang' ? 'selected' : '' }}>Uang Tunai</option>
            </select>

            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 text-white font-semibold text-xs hover:bg-slate-700 transition">
                Filter
            </button>
        </form>

        <a href="{{ route('pengepul.modal.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Beri Modal Baru</span>
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-4">No. Bukti</th>
                        <th class="py-3 px-4">Petani</th>
                        <th class="py-3 px-4">Bentuk</th>
                        <th class="py-3 px-4">Kategori & Rincian</th>
                        <th class="py-3 px-4 text-right">Nominal</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($modals as $m)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('pengepul.modal.show', $m) }}" class="text-emerald-700 hover:underline font-semibold text-xs">{{ $m->kode_modal }}</a>
                                <span class="block text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($m->tanggal_pemberian)->translatedFormat('d M Y') }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <span class="font-bold text-slate-800">{{ $m->petani->nama_petani }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->jenis_modal === 'barang' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                                    {{ ucfirst($m->jenis_modal) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-xs">
                                <strong class="text-slate-800">{{ $m->kategori_kebutuhan }}</strong>
                                <span class="block text-slate-500">{{ $m->rincian_barang ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($m->nominal, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pengepul.modal.show', $m) }}" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold">Rincian</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-sm">Belum ada data pemberian modal.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($modals->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $modals->links() }}</div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse($modals as $m)
            <a href="{{ route('pengepul.modal.show', $m) }}" class="block bg-white rounded-xl border border-slate-200/80 shadow-xs p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-700">{{ $m->kode_modal }}</span>
                    <span class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($m->tanggal_pemberian)->translatedFormat('d M Y') }}</span>
                </div>
                <p class="text-xs font-medium text-slate-800">{{ $m->petani->nama_petani }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $m->jenis_modal === 'barang' ? 'bg-blue-100 text-blue-800' : 'bg-emerald-100 text-emerald-800' }}">
                        {{ ucfirst($m->jenis_modal) }}
                    </span>
                    <span class="text-[10px] text-slate-500">{{ $m->kategori_kebutuhan }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 pt-2 border-t border-slate-100">
                    <span class="text-[10px] text-slate-500">{{ Str::limit($m->rincian_barang ?? '-', 30) }}</span>
                    <span class="text-sm font-bold text-slate-900">Rp {{ number_format($m->nominal, 0, ',', '.') }}</span>
                </div>
            </a>
        @empty
            <div class="py-12 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200/80">Belum ada data pemberian modal.</div>
        @endforelse
        @if($modals->hasPages())
            <div class="p-3">{{ $modals->links() }}</div>
        @endif
    </div>

</div>
@endsection

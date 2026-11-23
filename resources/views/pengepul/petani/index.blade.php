@extends('layouts.app')

@section('title', 'Data Petani Mitra')
@section('page_title', 'Data Petani Mitra')
@section('page_subtitle', 'Daftar petani mitra aktif')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Header Actions & Search -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs space-y-3 sm:space-y-0 sm:flex sm:items-center sm:justify-between sm:gap-4">
        <form method="GET" action="{{ route('pengepul.petani.index') }}" class="flex-1 max-w-md relative">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, kode, nomor WA..."
                class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </form>

        <a href="{{ route('pengepul.petani.create') }}" class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Petani</span>
        </a>
    </div>

    <!-- Desktop Table -->
    <div class="hidden md:block bg-white rounded-2xl sm:rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <tr>
                        <th class="py-3 px-4">Petani</th>
                        <th class="py-3 px-4">No. WA</th>
                        <th class="py-3 px-4 text-right">Total Modal</th>
                        <th class="py-3 px-4 text-right">Terbayar</th>
                        <th class="py-3 px-4 text-right">Sisa</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($petanis as $petani)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-4">
                                <a href="{{ route('pengepul.petani.show', $petani) }}" class="text-emerald-700 hover:underline font-bold text-xs">{{ $petani->nama_petani }}</a>
                                <span class="block text-[11px] text-slate-400">{{ $petani->alamat ?? 'Desa Bajuin' }}</span>
                            </td>
                            <td class="py-3 px-4 text-xs text-slate-600">{{ $petani->no_wa }}</td>
                            <td class="py-3 px-4 text-right text-xs font-medium text-slate-800">Rp {{ number_format($petani->total_modal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-xs font-medium text-emerald-600">Rp {{ number_format($petani->total_terbayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right">
                                @if($petani->sisa_kewajiban > 0)
                                    <span class="text-xs font-bold text-rose-600">Rp {{ number_format($petani->sisa_kewajiban, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-xs font-bold text-emerald-600">Lunas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $petani->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }}">
                                    {{ ucfirst($petani->status) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1">
                                    <a href="{{ route('pengepul.petani.show', $petani) }}" class="p-1.5 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-semibold">Detail</a>
                                    <a href="{{ route('pengepul.petani.edit', $petani) }}" class="p-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 text-sm">Tidak ada data petani.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($petanis->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $petanis->links() }}</div>
        @endif
    </div>

    <!-- Mobile Cards -->
    <div class="md:hidden space-y-3">
        @forelse($petanis as $petani)
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-xs p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="min-w-0 flex-1">
                        <a href="{{ route('pengepul.petani.show', $petani) }}" class="text-sm font-bold text-emerald-700 hover:underline truncate block">{{ $petani->nama_petani }}</a>
                        <p class="text-[10px] text-slate-400">{{ $petani->alamat ?? 'Desa Bajuin' }} &bull; {{ $petani->no_wa }}</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $petani->status === 'aktif' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-600' }} shrink-0 ml-2">
                        {{ ucfirst($petani->status) }}
                    </span>
                </div>
                <div class="grid grid-cols-3 gap-2 mt-2 pt-2 border-t border-slate-100 text-center">
                    <div>
                        <span class="text-[10px] text-slate-400 block">Modal</span>
                        <span class="text-xs font-bold text-slate-800">{{ number_format($petani->total_modal, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Terbayar</span>
                        <span class="text-xs font-bold text-emerald-600">{{ number_format($petani->total_terbayar, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 block">Sisa</span>
                        <span class="text-xs font-bold {{ $petani->sisa_kewajiban > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $petani->sisa_kewajiban > 0 ? number_format($petani->sisa_kewajiban, 0, ',', '.') : 'Lunas' }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center justify-end space-x-2 mt-3">
                    <a href="{{ route('pengepul.petani.show', $petani) }}" class="text-xs font-semibold text-emerald-600">Detail</a>
                    <a href="{{ route('pengepul.petani.edit', $petani) }}" class="text-xs font-semibold text-slate-500">Edit</a>
                </div>
            </div>
        @empty
            <div class="py-12 text-center text-slate-400 text-sm bg-white rounded-xl border border-slate-200/80">Tidak ada data petani.</div>
        @endforelse
        @if($petanis->hasPages())
            <div class="p-3">{{ $petanis->links() }}</div>
        @endif
    </div>

</div>
@endsection

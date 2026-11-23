@extends('layouts.app')

@section('title', 'Manajemen Akun Pengguna')
@section('page_title', 'Manajemen Akun Pengguna')
@section('page_subtitle', 'Kelola akun Admin, Pengepul, dan Petani dalam sistem')

@section('content')
<div class="space-y-4 sm:space-y-6">

    <!-- Stats Bar -->
    <div class="grid grid-cols-3 gap-3">
        <div class="bg-purple-50 border border-purple-200 rounded-xl p-3 text-center">
            <p class="text-lg font-extrabold text-purple-700">{{ $totalAdmin }}</p>
            <p class="text-[11px] text-purple-500 font-medium">Admin</p>
        </div>
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-center">
            <p class="text-lg font-extrabold text-amber-700">{{ $totalPengepul }}</p>
            <p class="text-[11px] text-amber-500 font-medium">Pengepul</p>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3 text-center">
            <p class="text-lg font-extrabold text-emerald-700">{{ $totalPetani }}</p>
            <p class="text-[11px] text-emerald-500 font-medium">Petani</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, username, email, atau no. telepon..."
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
            </div>
            <div>
                <select name="role" class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent">
                    <option value="">Semua Role</option>
                    <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="pengepul" {{ $roleFilter === 'pengepul' ? 'selected' : '' }}>Pengepul</option>
                    <option value="petani" {{ $roleFilter === 'petani' ? 'selected' : '' }}>Petani</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition">Cari</button>
                @if($search || $roleFilter)
                    <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-700 text-sm font-semibold hover:bg-slate-200 transition">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Action Bar -->
    <div class="flex items-center justify-between">
        <p class="text-sm text-slate-500">Menampilkan {{ $users->count() }} dari {{ $users->total() }} akun</p>
        <a href="{{ route('admin.users.create') }}" class="inline-flex items-center space-x-1.5 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-md transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span>Tambah Akun Baru</span>
        </a>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Pengguna</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Username</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider hidden sm:table-cell">Email</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider hidden md:table-cell">No. Telepon</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Role</th>
                        <th class="text-center px-5 py-3 text-xs font-bold text-slate-600 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center font-bold text-xs uppercase shrink-0
                                        @if($user->role === 'admin') bg-purple-100 text-purple-700
                                        @elseif($user->role === 'pengepul') bg-amber-100 text-amber-700
                                        @else bg-emerald-100 text-emerald-700
                                        @endif">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <p class="font-semibold text-slate-800">{{ $user->name }}</p>
                                            @if($user->role === 'petani' && $user->petani)
                                                <span class="inline-block font-mono text-[10px] font-bold text-emerald-800 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200">
                                                    {{ $user->petani->kode_petani }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-400">Bergabung {{ $user->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <code class="text-xs bg-slate-100 px-2 py-0.5 rounded-md text-slate-700">{{ $user->username }}</code>
                            </td>
                            <td class="px-5 py-3.5 hidden sm:table-cell text-slate-600">{{ $user->email ?? '-' }}</td>
                            <td class="px-5 py-3.5 hidden md:table-cell text-slate-600">{{ $user->phone ?? '-' }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="text-[11px] px-2.5 py-1 rounded-full font-semibold border
                                    @if($user->role === 'admin') bg-purple-50 text-purple-700 border-purple-200
                                    @elseif($user->role === 'pengepul') bg-amber-50 text-amber-700 border-amber-200
                                    @else bg-emerald-50 text-emerald-700 border-emerald-200
                                    @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}" class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus akun {{ $user->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <p class="text-sm font-medium">Tidak ada akun ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="flex justify-center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection

<!DOCTYPE html>
<html lang="id" class="h-full bg-[#f7faf7]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaniKelola') - Sistem Pengelolaan Modal & Panen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; font-feature-settings: "liga" 0, "calt" 0; }
        @media (min-width: 768px) {
            .md\:block, [class~="md:block"] { display: block !important; }
            .md\:flex, [class~="md:flex"] { display: flex !important; }
            .md\:hidden, [class~="md:hidden"] { display: none !important; }
            .md\:grid, [class~="md:grid"] { display: grid !important; }
        }
        /* Floating pill: mengecil saat scroll, melebar lagi saat di-hover. */
        #floatingPill { transition: all .3s ease; }
        #floatingPill .nav-label, #floatingPill .brand-text { transition: opacity .2s ease; }
        #floatingPill.pill-shrunk .nav-label, #floatingPill.pill-shrunk .brand-text { display: none; }
        #floatingPill.pill-shrunk { padding-left: .5rem; padding-right: .5rem; }
        #floatingPill.pill-shrunk:hover .nav-label, #floatingPill.pill-shrunk:hover .brand-text { display: inline; }
        #floatingPill.pill-shrunk:hover { padding-left: .75rem; padding-right: .75rem; }

        /* Sembunyikan ikon mata bawaan browser (Edge/Chrome) pada field password,
           supaya hanya ikon mata kita sendiri yang tampil (tidak dobel). */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear { display: none; }
        input[type="password"]::-webkit-credentials-auto-fill-button { visibility: hidden; }
    </style>
</head>
<body class="h-full antialiased text-slate-800 flex flex-col min-h-screen bg-[#f7faf7] overflow-x-hidden">

    {{-- Top bar sederhana, HP saja. Menu utama ada di bottom nav. --}}
    <header class="md:hidden bg-white border-b border-slate-200 px-4 py-3 flex items-center justify-between sticky top-0 z-40">
        <div class="flex items-center space-x-2">
            <div class="w-9 h-9 rounded-full bg-white border border-slate-200 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
            <div>
                <span class="font-extrabold text-base tracking-tight text-[#14532d]">TaniKelola</span>
                <span class="text-[10px] block text-slate-500 font-medium">Bajuin, Tanah Laut</span>
            </div>
        </div>
        <a href="{{ route('profile.show') }}" class="w-8 h-8 rounded-full bg-[#14532d] text-white flex items-center justify-center font-bold text-xs uppercase">
            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
        </a>
    </header>

    {{-- Floating pill, laptop saja. Melayang di tengah atas. --}}
    <nav id="floatingPill" class="hidden md:flex fixed top-4 left-1/2 -translate-x-1/2 z-50 max-w-[96vw] items-center gap-1 rounded-full bg-[#14532d] px-3 py-2 shadow-xl shadow-[#14532d]/30 border-b-4 border-[#eab308]">
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->role === 'pengepul' ? route('pengepul.dashboard') : route('petani.dashboard')) }}" class="flex items-center gap-2 pr-2 shrink-0">
            <span class="w-8 h-8 rounded-full bg-white flex items-center justify-center overflow-hidden shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
            </span>
            <span class="brand-text font-extrabold text-white text-sm whitespace-nowrap">TaniKelola</span>
        </a>
        <span class="w-px h-6 bg-white/20 shrink-0"></span>

        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('admin.dashboard') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('admin.users.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span class="nav-label">Akun</span>
            </a>
        @elseif(auth()->user()->role === 'pengepul')
            <a href="{{ route('pengepul.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.dashboard') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="nav-label">Dashboard</span>
            </a>
            <a href="{{ route('pengepul.transaksi.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.transaksi.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                <span class="nav-label">Panen</span>
            </a>
            <a href="{{ route('pengepul.modal.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.modal.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="nav-label">Modal</span>
            </a>
            <a href="{{ route('pengepul.pembayaran.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.pembayaran.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="nav-label">Bayar</span>
            </a>
            <a href="{{ route('pengepul.petani.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.petani.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="nav-label">Petani</span>
            </a>
            <a href="{{ route('pengepul.laporan.index') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('pengepul.laporan.*') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="nav-label">Laporan</span>
            </a>
            <button type="button" id="pillMoreBtn" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap text-white/80 hover:bg-white/10 hover:text-white transition shrink-0">
                <span class="nav-label">Lainnya</span>
                <span aria-hidden="true">&#8226;&#8226;&#8226;</span>
            </button>
        @else
            <a href="{{ route('petani.dashboard') }}" class="flex items-center gap-1.5 px-3 py-2 rounded-full text-xs font-bold whitespace-nowrap transition {{ request()->routeIs('petani.dashboard') ? 'bg-[#eab308] text-[#14532d]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="nav-label">Status Saya</span>
            </a>
        @endif

        @if(auth()->user()->role === 'pengepul')
            <a href="{{ route('pengepul.transaksi.create') }}" class="ml-1 shrink-0 inline-flex items-center px-3 py-2 rounded-full bg-[#eab308] text-[#14532d] text-xs font-extrabold hover:bg-[#facc15] transition whitespace-nowrap">+ Catat Panen</a>
        @endif
    </nav>

    {{-- Panel "Lainnya" di bawah floating pill, laptop saja. --}}
    <div id="pillMorePanel" class="fixed top-[76px] left-1/2 -translate-x-1/2 z-50 w-72 rounded-lg bg-white border border-slate-200 shadow-xl p-2" style="display:none;">
        <p class="px-3 pt-2 pb-1 text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ auth()->user()->name ?? '' }} &middot; {{ auth()->user()->role ?? '' }}</p>
        <a href="{{ route('profile.show') }}" class="block px-3 py-2 rounded-md text-sm font-bold text-[#14532d] hover:bg-[#f7faf7]">Profil Saya</a>
        @if(auth()->user()->role === 'pengepul')
            <a href="{{ route('pengepul.komoditas.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Komoditas Sayur</a>
            <a href="{{ route('pengepul.wa_log.index') }}" class="block px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">WhatsApp Gateway</a>
        @endif
    </div>

    <!-- Main Content Area -->
    <main id="mainScroll" class="flex-1 flex flex-col min-w-0 overflow-y-auto pt-4 md:pt-28 pb-24 md:pb-0">
        <!-- Top Banner Header -->
        <div class="px-4 sm:px-6 max-w-7xl mx-auto w-full">
            <div class="bg-white border border-slate-200 rounded-lg px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between">
                <div class="min-w-0 flex-1">
                    <h2 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight truncate">@yield('page_title', 'TaniKelola')</h2>
                    <p class="text-[10px] sm:text-xs text-slate-500 hidden sm:block truncate">@yield('page_subtitle', 'Sistem Informasi Pengelolaan Modal & Penjualan Hasil Panen')</p>
                </div>
                <div class="flex items-center space-x-3">
                    <span class="text-xs text-slate-500 hidden sm:inline">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Alert Notifications -->
        <div class="px-4 sm:px-6 max-w-7xl mx-auto w-full pt-4 sm:pt-6">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3">
                    <span class="text-xl">&#9989;</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3">
                    <span class="text-xl">&#9888;&#65039;</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 rounded-lg bg-blue-50 border border-blue-200 text-blue-800 flex items-start space-x-3">
                    <span class="text-xl">&#8505;&#65039;</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-amber-50 border border-amber-200 text-amber-900">
                    <div class="font-bold text-sm mb-1">Periksa kembali formulir Anda:</div>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Page Dynamic Body -->
        <div class="px-4 sm:px-6 max-w-7xl mx-auto w-full">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto px-6 py-4 text-center text-xs text-slate-400 border-t border-slate-200">
            &copy; {{ date('Y') }} TaniKelola &bull; Muhammad Yoga &bull; TI Politeknik Negeri Tanah Laut
        </footer>
    </main>

    {{-- Bottom nav, HP saja. --}}
    <nav class="md:hidden fixed bottom-0 left-0 right-0 w-full z-50 bg-white border-t border-slate-200">
        @if(auth()->user()->role === 'pengepul')
            <div class="grid grid-cols-5 w-full items-end pb-2 pt-1">
                <a href="{{ route('pengepul.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold {{ request()->routeIs('pengepul.dashboard') ? 'text-[#14532d]' : 'text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Beranda
                </a>
                <a href="{{ route('pengepul.transaksi.index') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold {{ request()->routeIs('pengepul.transaksi.*') ? 'text-[#14532d]' : 'text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    Panen
                </a>
                <a href="{{ route('pengepul.transaksi.create') }}" class="flex flex-col items-center -mt-6">
                    <span class="w-12 h-12 rounded-full bg-[#eab308] text-[#14532d] border-4 border-white shadow-lg flex items-center justify-center text-2xl font-extrabold leading-none">+</span>
                    <span class="text-[11px] font-bold text-[#14532d] mt-0.5">Catat</span>
                </a>
                <a href="{{ route('pengepul.modal.index') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold {{ request()->routeIs('pengepul.modal.*') ? 'text-[#14532d]' : 'text-slate-500' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Modal
                </a>
                <button type="button" id="mobileMoreBtn" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-slate-500">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    Lainnya
                </button>
            </div>
        @elseif(auth()->user()->role === 'admin')
            <div class="grid grid-cols-3 w-full items-end pb-2 pt-1">
                <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-[#14532d]">Beranda</a>
                <a href="{{ route('admin.users.index') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-slate-500">Akun</a>
                <button type="button" id="mobileMoreBtn" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-slate-500">Lainnya</button>
            </div>
        @else
            <div class="grid grid-cols-2 w-full items-end pb-2 pt-1">
                <a href="{{ route('petani.dashboard') }}" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-[#14532d]">Status Saya</a>
                <button type="button" id="mobileMoreBtn" class="flex flex-col items-center gap-0.5 text-[11px] font-bold text-slate-500">Lainnya</button>
            </div>
        @endif
    </nav>

    {{-- Panel "Lainnya", HP saja, muncul di atas bottom nav. --}}
    <div id="mobileMorePanel" class="md:hidden fixed bottom-[76px] left-4 right-4 z-50 rounded-lg bg-white border border-slate-200 shadow-xl p-2" style="display:none;">
        <a href="{{ route('profile.show') }}" class="block px-3 py-2.5 rounded-md text-sm font-bold text-[#14532d] hover:bg-[#f7faf7]">Profil Saya</a>
        @if(auth()->user()->role === 'pengepul')
            <a href="{{ route('pengepul.pembayaran.index') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Pembayaran Kewajiban</a>
            <a href="{{ route('pengepul.petani.index') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Data Petani Mitra</a>
            <a href="{{ route('pengepul.komoditas.index') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Komoditas Sayur</a>
            <a href="{{ route('pengepul.laporan.index') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Laporan &amp; Rekapitulasi</a>
            <a href="{{ route('pengepul.wa_log.index') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">WhatsApp Gateway</a>
        @elseif(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Dashboard</a>
        @else
            <a href="{{ route('petani.dashboard') }}" class="block px-3 py-2.5 rounded-md text-sm font-medium text-slate-700 hover:bg-[#f7faf7]">Status &amp; Riwayat Saya</a>
        @endif
    </div>

    <script>
        // Floating pill mengecil saat konten di-scroll, melebar lagi saat di-hover (CSS).
        const mainScroll = document.getElementById('mainScroll');
        const pill = document.getElementById('floatingPill');
        if (mainScroll && pill) {
            mainScroll.addEventListener('scroll', () => {
                pill.classList.toggle('pill-shrunk', mainScroll.scrollTop > 60);
            });
            // Kalau halaman di-scroll lewat window (bukan main), cek juga.
            window.addEventListener('scroll', () => {
                pill.classList.toggle('pill-shrunk', window.scrollY > 60);
            });
        }
        // Panel "Lainnya" di floating pill.
        const pillMoreBtn = document.getElementById('pillMoreBtn');
        const pillMorePanel = document.getElementById('pillMorePanel');
        if (pillMoreBtn && pillMorePanel) {
            pillMoreBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                pillMorePanel.style.display = pillMorePanel.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', (e) => {
                if (!pillMorePanel.contains(e.target)) pillMorePanel.style.display = 'none';
            });
        }
        // Panel "Lainnya" di bottom nav HP.
        const mobileMoreBtn = document.getElementById('mobileMoreBtn');
        const mobileMorePanel = document.getElementById('mobileMorePanel');
        if (mobileMoreBtn && mobileMorePanel) {
            mobileMoreBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                mobileMorePanel.style.display = mobileMorePanel.style.display === 'none' ? 'block' : 'none';
            });
            document.addEventListener('click', (e) => {
                if (!mobileMorePanel.contains(e.target)) mobileMorePanel.style.display = 'none';
            });
        }
    </script>
    @stack('scripts')
</body>
</html>

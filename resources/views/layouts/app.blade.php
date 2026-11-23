<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'TaniKelola') - Sistem Pengelolaan Modal & Panen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @media (min-width: 768px) {
            .md\:block, [class~="md:block"] { display: block !important; }
            .md\:flex, [class~="md:flex"] { display: flex !important; }
            .md\:hidden, [class~="md:hidden"] { display: none !important; }
            .md\:grid, [class~="md:grid"] { display: grid !important; }
        }
    </style>
</head>
<body class="h-full antialiased text-slate-800 flex flex-col md:flex-row min-h-screen bg-slate-50">

    <!-- Mobile Top Navigation -->
    <header class="md:hidden bg-emerald-900 text-white p-4 flex items-center justify-between shadow-md sticky top-0 z-50">
        <div class="flex items-center space-x-2">
            <div class="w-9 h-9 rounded-xl bg-emerald-500/30 flex items-center justify-center overflow-hidden">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
            <div>
                <span class="font-extrabold text-lg tracking-tight">TaniKelola</span>
                <span class="text-[10px] block text-emerald-300 font-medium">Bajuin, Tanah Laut</span>
            </div>
        </div>
        <button id="mobileMenuBtn" class="p-2 rounded-lg bg-emerald-800 text-emerald-100 hover:bg-emerald-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </header>

    <!-- Sidebar Navigation for Desktop & Mobile Overlay -->
    <aside id="sidebar" class="hidden md:flex flex-col w-72 bg-gradient-to-b from-emerald-950 via-slate-900 to-emerald-950 text-white shrink-0 border-r border-emerald-900/40 shadow-xl fixed md:sticky top-0 h-screen z-40 overflow-y-auto">
        <!-- Brand Header -->
        <div class="p-6 border-b border-emerald-800/40">
            <div class="flex items-center space-x-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-400 flex items-center justify-center shadow-lg shadow-emerald-500/20 overflow-hidden">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="font-extrabold text-xl tracking-tight text-white flex items-center">
                        TaniKelola
                    </h1>
                    <p class="text-xs text-emerald-400/80 font-medium">Mitra Petani & Pengepul</p>
                </div>
            </div>

            <!-- Active User Info Pill -->
            <div class="mt-5 p-3 rounded-xl bg-white/5 border border-white/10 flex items-center justify-between">
                <div class="flex items-center space-x-2.5 overflow-hidden">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="truncate">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                        <p class="text-[11px] text-emerald-400 capitalize flex items-center">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-1"></span>
                            {{ auth()->user()->role }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 py-4 px-3 space-y-1">
            @if(auth()->user()->role === 'admin')
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-emerald-400/70">Menu Administrator</div>
                
                <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.users.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Manajemen Akun</span>
                </a>

            @elseif(auth()->user()->role === 'pengepul')
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-emerald-400/70">Menu Pengepul</div>
                
                <a href="{{ route('pengepul.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('pengepul.transaksi.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.transaksi.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Transaksi Penjualan</span>
                </a>

                <a href="{{ route('pengepul.modal.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.modal.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Pemberian Modal</span>
                </a>

                <a href="{{ route('pengepul.pembayaran.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.pembayaran.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Pembayaran Kewajiban</span>
                </a>

                <div class="pt-4 pb-2 px-3 text-[11px] font-bold uppercase tracking-wider text-emerald-400/70">Master Data & Laporan</div>

                <a href="{{ route('pengepul.petani.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.petani.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Data Petani Mitra</span>
                </a>

                <a href="{{ route('pengepul.komoditas.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.komoditas.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    <span>Komoditas Sayur</span>
                </a>

                <a href="{{ route('pengepul.laporan.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.laporan.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan & Rekapitulasi</span>
                </a>

                <a href="{{ route('pengepul.wa_log.index') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('pengepul.wa_log.*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    <div class="flex items-center justify-between w-full">
                        <span>WhatsApp Gateway</span>
                        <span class="text-[10px] bg-emerald-500/20 text-emerald-300 px-1.5 py-0.5 rounded-md border border-emerald-500/30">Fonnte</span>
                    </div>
                </a>
            @else
                <!-- Menu Petani -->
                <div class="px-3 pb-2 text-[11px] font-bold uppercase tracking-wider text-emerald-400/70">Portal Petani</div>

                <a href="{{ route('petani.dashboard') }}" class="flex items-center space-x-3 px-3 py-2.5 rounded-xl text-sm font-medium transition {{ request()->routeIs('petani.dashboard') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span>Status & Riwayat Saya</span>
                </a>
            @endif
        </div>

        <!-- Sidebar Footer & Logout -->
        <div class="p-4 border-t border-emerald-800/40">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2.5 rounded-xl text-sm font-medium text-rose-300 bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/20 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Keluar (Logout)</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        <!-- Top Banner Header -->
        <div class="bg-white border-b border-slate-200 px-4 sm:px-6 py-3 sm:py-4 flex items-center justify-between shadow-xs">
            <div class="min-w-0 flex-1">
                <h2 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight truncate">@yield('page_title', 'TaniKelola')</h2>
                <p class="text-[10px] sm:text-xs text-slate-500 hidden sm:block truncate">@yield('page_subtitle', 'Sistem Informasi Pengelolaan Modal & Penjualan Hasil Panen')</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-xs text-slate-500 hidden sm:inline">{{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}</span>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span>
                    Sistem Aktif
                </span>
            </div>
        </div>

        <!-- Alert Notifications -->
        <div class="p-4 sm:p-6 pb-0">
            @if(session('success'))
                <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start space-x-3 shadow-xs">
                    <span class="text-xl">✅</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start space-x-3 shadow-xs">
                    <span class="text-xl">⚠️</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('error') }}</div>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 flex items-start space-x-3 shadow-xs">
                    <span class="text-xl">ℹ️</span>
                    <div class="text-sm font-medium pt-0.5">{{ session('info') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900">
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
        <div class="p-4 sm:p-6">
            @yield('content')
        </div>

        <!-- Footer -->
        <footer class="mt-auto px-6 py-4 text-center text-xs text-slate-400 border-t border-slate-200">
            &copy; {{ date('Y') }} TaniKelola &bull; Proposal TA Muhammad Yoga (2401301003) &bull; TI Politeknik Negeri Tanah Laut
        </footer>
    </main>

    <script>
        // Toggle mobile menu
        const menuBtn = document.getElementById('mobileMenuBtn');
        const sidebar = document.getElementById('sidebar');
        if (menuBtn && sidebar) {
            menuBtn.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                sidebar.classList.toggle('flex');
            });
        }
    </script>
    @stack('scripts')
</body>
</html>

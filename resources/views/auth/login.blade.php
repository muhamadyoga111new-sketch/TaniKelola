<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TaniKelola (Sistem Modal & Penjualan Hasil Panen)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full flex items-center justify-center p-4 sm:p-6 bg-radial-[at_top_right] from-emerald-950 via-slate-950 to-black text-slate-100">

    <div class="w-full max-w-md">
        <!-- Logo and Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 shadow-xl shadow-emerald-500/20 mb-4 border border-emerald-400/30 overflow-hidden" style="width: 96px; height: 96px;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-cover">
            </div>
            
            <p class="text-sm text-emerald-400/90 font-medium mt-1">Sistem Pengelolaan Modal & Transaksi Hasil Panen</p>
        </div>

        <!-- Main Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/15 p-6 sm:p-8 rounded-3xl shadow-2xl">
            <h2 class="text-lg font-bold text-white mb-2">Masuk ke Sistem</h2>
            <p class="text-xs text-slate-300 mb-6">Gunakan akun Admin, Pengepul, atau Petani untuk melanjutkan.</p>

            @if(session('error'))
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs flex items-center space-x-2">
                    <span>⚠️</span>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 text-xs flex items-center space-x-2">
                    <span>✅</span>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-200 mb-1.5">
                        Username
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <input type="text" name="login" id="loginInput" value="{{ old('login') }}" required autofocus
                            placeholder="Contoh: admin, pengepul, atau subarjo"
                            class="w-full pl-10 pr-4 py-2.5 bg-black/30 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                    </div>
                    @error('login')
                        <p class="text-rose-400 text-[11px] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-200 mb-1.5">
                        Kata Sandi (Password)
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input type="password" name="password" id="passwordInput" required
                            placeholder="••••••••"
                            class="w-full pl-10 pr-10 py-2.5 bg-black/30 border border-white/10 rounded-xl text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent transition">
                        <button type="button" id="togglePassword" title="Tampilkan / Sembunyikan Kata Sandi"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-emerald-400 focus:outline-none transition">
                            <!-- Icon Eye (Tampilkan) -->
                            <svg id="eyeIcon" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <!-- Icon Eye Off (Sembunyikan) -->
                            <svg id="eyeOffIcon" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-300">
                    <label class="flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-slate-700 text-emerald-500 focus:ring-emerald-400 bg-black/30">
                        <span>Ingat saya</span>
                    </label>
                    
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-400 hover:to-teal-400 text-slate-950 font-bold text-sm shadow-lg shadow-emerald-500/25 transition-all duration-200 transform active:scale-98">
                    Masuk ke Sistem
                </button>
            </form>


        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Tugas Akhir Program Studi TI &bull; Politeknik Negeri Tanah Laut 2026
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');

            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    eyeIcon.classList.toggle('hidden', isPassword);
                    eyeOffIcon.classList.toggle('hidden', !isPassword);
                });
            }
        });
    </script>
</body>
</html>

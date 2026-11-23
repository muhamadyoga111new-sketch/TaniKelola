<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Pengepul\DashboardController as PengepulDashboardController;
use App\Http\Controllers\Pengepul\PetaniController;
use App\Http\Controllers\Pengepul\KomoditasController;
use App\Http\Controllers\Pengepul\ModalController;
use App\Http\Controllers\Pengepul\TransaksiPanenController;
use App\Http\Controllers\Pengepul\PembayaranKewajibanController;
use App\Http\Controllers\Pengepul\LaporanController;
use App\Http\Controllers\Pengepul\WaLogController;
use App\Http\Controllers\Petani\DashboardController as PetaniDashboardController;
use Illuminate\Support\Facades\Route;

// Redirect Root
Route::get('/', function () {
    if (auth()->check()) {
        $role = auth()->user()->role;
        if ($role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return $role === 'pengepul' 
            ? redirect()->route('pengepul.dashboard') 
            : redirect()->route('petani.dashboard');
    }
    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rute Admin (Super Administrator)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Akun Pengguna (Admin, Pengepul, Petani)
    Route::resource('users', UserManagementController::class)->except(['show']);
});

// Rute Pengepul
Route::middleware(['auth', 'role:pengepul'])->prefix('pengepul')->name('pengepul.')->group(function () {
    Route::get('/dashboard', [PengepulDashboardController::class, 'index'])->name('dashboard');

    // Manajemen Data Petani
    Route::resource('petani', PetaniController::class);

    // Manajemen Komoditas Sayuran
    Route::resource('komoditas', KomoditasController::class)->except(['show']);

    // Pencatatan Modal Petani
    Route::resource('modal', ModalController::class)->except(['edit', 'update']);

    // Transaksi Penjualan Hasil Panen
    Route::resource('transaksi', TransaksiPanenController::class)->except(['edit', 'update']);
    Route::get('/transaksi/{transaksi}/cetak', [TransaksiPanenController::class, 'cetak'])->name('transaksi.cetak');
    Route::post('/transaksi/{transaksi}/kirim-wa', [TransaksiPanenController::class, 'kirimWa'])->name('transaksi.kirim_wa');

    // Pembayaran Kewajiban Tunai (Di Luar Panen)
    Route::resource('pembayaran', PembayaranKewajibanController::class)->except(['edit', 'update']);

    // Laporan & Rekapitulasi
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/cetak', [LaporanController::class, 'cetak'])->name('laporan.cetak');

    // WhatsApp Gateway Fonnte Logs & Test
    Route::get('/wa-log', [WaLogController::class, 'index'])->name('wa_log.index');
    Route::post('/wa-log/test', [WaLogController::class, 'testSend'])->name('wa_log.test');
});

// Rute Petani (Portal Mandiri Petani)
Route::middleware(['auth', 'role:petani'])->prefix('petani')->name('petani.')->group(function () {
    Route::get('/dashboard', [PetaniDashboardController::class, 'index'])->name('dashboard');
});

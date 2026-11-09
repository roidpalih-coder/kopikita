<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Support\Facades\Route;

// Rute Publik / Tamu (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Rute Autentikasi Sistem
Route::middleware('auth')->group(function () {
    // 1. Dashboard Utama
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // 2. POS Transaksi Kasir
    Route::get('/pos', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::post('/pos', [TransaksiController::class, 'store'])->name('transaksi.store');

    // 3. Manajemen Data Menu (CRUD)
    Route::get('/menu/export', [MenuController::class, 'export'])->name('menu.export');
    Route::post('/menu/import', [MenuController::class, 'import'])->name('menu.import');
    Route::resource('menu', MenuController::class)->except(['show']);

    // 4. Laporan Penjualan & Detail Struk
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');
    Route::get('/laporan/export/csv', [LaporanController::class, 'exportCsv'])->name('laporan.export.csv');
    Route::get('/laporan/{transaksi}/struk', [LaporanController::class, 'cetakStruk'])->name('laporan.struk');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

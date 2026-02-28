<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WargaController;
use Illuminate\Support\Facades\Route;

// 1. Ubah bagian ini untuk menampilkan halaman Landing Page
Route::get('/', function () {
    return view('landing'); // Pastikan file resources/views/landing.blade.php sudah ada
})->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

// Route untuk fitur Cek Surat (diakses dari tombol di Landing Page)
Route::get('/cek-surat', [TrackingController::class, 'index'])->name('publik.surat');
Route::post('/cek-surat/cari', [TrackingController::class, 'cari'])->name('publik.surat.cari');
Route::get('/cek-surat/session', [TrackingController::class, 'session'])->name('publik.surat.session');
Route::get('/cek-surat/list', [TrackingController::class, 'list'])->name('publik.surat.list');
Route::get('/cek-surat/detail/{id}', [TrackingController::class, 'detail'])->name('publik.surat.detail');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('admin', AdminController::class)->names('admin')->except(['show']);

    Route::get('/warga/search', [WargaController::class, 'cariJson'])->name('warga.search');
    Route::resource('warga', WargaController::class)->names('warga')->except(['show']);

    Route::post('/pengajuan/{pengajuan}/status', [PengajuanController::class, 'updateStatus'])->name('pengajuan.status');
    Route::resource('pengajuan', PengajuanController::class)->names('pengajuan');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

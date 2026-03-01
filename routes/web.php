<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SuratTemplateController;
use App\Http\Controllers\TrackingController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\WargaImportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
})->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::get('/cek-surat', [TrackingController::class, 'index'])->name('publik.surat');
Route::post('/cek-surat/cari', [TrackingController::class, 'cari'])->name('publik.surat.cari');
Route::get('/cek-surat/session', [TrackingController::class, 'session'])->name('publik.surat.session');
Route::get('/cek-surat/list', [TrackingController::class, 'list'])->name('publik.surat.list');
Route::get('/cek-surat/detail/{id}', [TrackingController::class, 'detail'])->name('publik.surat.detail');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

    Route::middleware('roles:admin,warga')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('pengajuan', PengajuanController::class)
            ->names('pengajuan')
            ->only(['index', 'create', 'store', 'show']);

        Route::resource('pengaduan', PengaduanController::class)
            ->names('pengaduan')
            ->only(['index', 'create', 'store', 'show']);
    });

    Route::middleware('roles:warga')->group(function () {
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    });

    Route::middleware('roles:admin')->group(function () {
        Route::resource('admin', AdminController::class)->names('admin')->except(['show']);

        Route::get('/warga/search', [WargaController::class, 'cariJson'])->name('warga.search');
        Route::get('/warga/import', [WargaImportController::class, 'page'])->name('warga.import.page');
        Route::post('/warga/import', [WargaImportController::class, 'import'])->name('warga.import.store');
        Route::post('/warga/bulk-delete', [WargaController::class, 'bulkDestroy'])->name('warga.bulk-delete');
        Route::resource('warga', WargaController::class)->names('warga')->except(['show']);

        Route::post('/pengajuan/{pengajuan}/status', [PengajuanController::class, 'updateStatus'])->name('pengajuan.status');
        Route::get('/pengajuan/{pengajuan}/download-docx', [PengajuanController::class, 'downloadDocx'])->name('pengajuan.download-docx');
        Route::post('/pengajuan/{pengajuan}/upload-signed-pdf', [PengajuanController::class, 'uploadSignedPdf'])->name('pengajuan.upload-signed-pdf');
        Route::resource('pengajuan', PengajuanController::class)
            ->names('pengajuan')
            ->only(['edit', 'update', 'destroy']);

        Route::resource('surat-template', SuratTemplateController::class)
            ->names('surat-template')
            ->except(['show']);

        Route::post('/pengaduan/{pengaduan}/status', [PengaduanController::class, 'updateStatus'])->name('pengaduan.status');
        Route::delete('/pengaduan/{pengaduan}', [PengaduanController::class, 'destroy'])->name('pengaduan.destroy');
    });

    Route::middleware('roles:admin,warga')->group(function () {
        Route::get('/pengajuan/{pengajuan}/download-signed-pdf', [PengajuanController::class, 'downloadSignedPdf'])
            ->name('pengajuan.download-signed-pdf');
    });
});

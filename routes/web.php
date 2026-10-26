<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // PEMILIK
    Route::middleware('role:pemilik')->prefix('pemilik')->group(function () {
        Route::get('/dashboard', fn () => view('pemilik.dashboard'))->name('pemilik.dashboard');
    });

    // KASIR (admin juga boleh menjual)
    Route::middleware('role:kasir,admin')->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
        Route::get('/kasir/cari', [KasirController::class, 'cari'])->name('kasir.cari');
        Route::post('/kasir/checkout', [KasirController::class, 'checkout'])->name('kasir.checkout');
    });

    // Nota
    Route::middleware('role:kasir,admin,pemilik')->group(function () {
        Route::get('/kasir/nota/{transaksi}', [KasirController::class, 'nota'])->name('kasir.nota');
    });

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', fn () => redirect()->route('barang.index'))->name('admin.dashboard');
    });

    // CRUD BARANG
    Route::middleware('role:admin,pemilik')->group(function () {
        Route::resource('barang', ProdukController::class)->parameters(['barang' => 'produk']);
    });
});
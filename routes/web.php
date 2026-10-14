<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ProdukController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));

// Login/logout TANPA middleware guest
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {

    // PEMILIK
    Route::middleware('role:pemilik')->prefix('pemilik')->group(function () {
        Route::get('/dashboard', fn () => view('pemilik.dashboard'))->name('pemilik.dashboard');
    });

    // KASIR
    Route::middleware('role:kasir')->group(function () {
        Route::get('/kasir', [KasirController::class, 'index'])->name('kasir');
    });

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('/dashboard', fn () => redirect()->route('barang.index'))->name('admin.dashboard');
    });

    // CRUD BARANG (admin + pemilik boleh akses)
    Route::middleware('role:admin,pemilik')->group(function () {
        Route::resource('barang', ProdukController::class)->parameters(['barang' => 'produk']);
    });
});
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\KategoriController;
use App\Http\Controllers\BarangTemuanController;
use App\Http\Controllers\LaporanKehilanganController;
use App\Http\Controllers\LaporanKerusakanController;
use App\Http\Controllers\KlaimBarangController;

// Auth (publik - tanpa login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rute terproteksi (harus login dulu, pakai token)
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Kontroler Terpadu (menangani web & API via isApiRequest)
    Route::apiResource('kategori', KategoriController::class);
    Route::apiResource('barang-temuan', BarangTemuanController::class);
    Route::apiResource('laporan-kehilangan', LaporanKehilanganController::class);
    Route::apiResource('laporan-kerusakan', LaporanKerusakanController::class);
    Route::apiResource('klaim-barang', KlaimBarangController::class);
});

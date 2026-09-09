<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Autentikasi untuk Mobile (dengan rate limiting)
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register'])->middleware('throttle:6,1');
Route::post('/forgot-password', [\App\Http\Controllers\Api\AuthController::class, 'forgotPassword'])->middleware('throttle:5,1');
Route::post('/verify-otp', [\App\Http\Controllers\Api\AuthController::class, 'verifyOtp'])->middleware('throttle:6,1');
Route::post('/reset-password', [\App\Http\Controllers\Api\AuthController::class, 'resetPassword'])->middleware('throttle:6,1');

Route::get('/artikel', [\App\Http\Controllers\Api\ArtikelController::class, 'index']);

// Protected Routes (Harus Login dan Bawa Token Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    // Info User Saat Ini
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('/profil/update', [\App\Http\Controllers\Api\AuthController::class, 'updateProfile']);
    Route::post('/profil/password', [\App\Http\Controllers\Api\AuthController::class, 'updatePassword']);
    Route::post('/update-fcm-token', [\App\Http\Controllers\Api\AuthController::class, 'updateFcmToken']);

    // Data Master untuk Form Laporan (Mobile)
    Route::get('/kategori', function () {
        return response()->json(['success' => true, 'data' => \App\Models\Kategori::all()]);
    });
    Route::get('/wilayah', function () {
        return response()->json(['success' => true, 'data' => \App\Models\Wilayah::all()]);
    });

    // API Khusus Pelapor
    Route::prefix('pelapor')->middleware('role:pelapor')->group(function () {
        Route::get('/laporan/publik', [\App\Http\Controllers\Api\LaporanController::class, 'publik']);
        Route::get('/laporan', [\App\Http\Controllers\Api\LaporanController::class, 'index']);
        Route::post('/laporan', [\App\Http\Controllers\Api\LaporanController::class, 'store']);
        Route::post('/laporan/{id}/ulasan', [\App\Http\Controllers\Api\LaporanController::class, 'storeUlasan']);
        Route::post('/laporan/{id}/konfirmasi', [\App\Http\Controllers\Api\LaporanController::class, 'konfirmasi']);
        
        Route::get('/notifikasi', [\App\Http\Controllers\Api\NotifikasiController::class, 'index']);
        Route::post('/notifikasi/{id}/baca', [\App\Http\Controllers\Api\NotifikasiController::class, 'markAsRead']);
    });

    // API Khusus Petugas
    Route::prefix('petugas')->middleware('role:petugas')->group(function () {
        Route::get('/tugas', [\App\Http\Controllers\Api\TugasController::class, 'index']);
        Route::post('/tugas/{id}/verifikasi', [\App\Http\Controllers\Api\TugasController::class, 'verifikasi']);
        
        Route::get('/notifikasi', [\App\Http\Controllers\Api\NotifikasiController::class, 'index']);
        Route::post('/notifikasi/{id}/baca', [\App\Http\Controllers\Api\NotifikasiController::class, 'markAsRead']);
    });
});

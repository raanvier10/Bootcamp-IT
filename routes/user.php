<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\UserController;

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
|
| Dashboard user, laporan, profil
| Prefix: /user
| Middleware: auth, role:user
|
*/

Route::middleware(['auth', 'role:pengguna'])->prefix('user')->name('user.')->group(function () {

    // FR-US-01: Dashboard User
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // FR-US-02/05: Buat Laporan
    Route::get('/laporan/buat', [UserController::class, 'createReport'])->name('report.create');
    Route::post('/laporan/buat', [UserController::class, 'storeReport'])->name('report.store');

    // FR-US-10: Riwayat Laporan
    Route::get('/laporan', [UserController::class, 'reports'])->name('reports');

    // FR-US-06: Detail & Tracking Laporan
    Route::get('/laporan/{id}', [UserController::class, 'reportDetail'])->name('report.detail');

    // FR-US-08: Konfirmasi Laporan Selesai
    Route::post('/laporan/{id}/konfirmasi', [UserController::class, 'confirmReport'])->name('report.confirm');

    // FR-US-09: Rating dan Feedback
    Route::post('/laporan/{id}/feedback', [UserController::class, 'submitFeedback'])->name('report.feedback');

    // FR-US-11: Profil
    Route::get('/profil', [UserController::class, 'profile'])->name('profile');
    Route::put('/profil', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [UserController::class, 'updatePassword'])->name('profile.password');
});

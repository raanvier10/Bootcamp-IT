<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Guest\GuestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Halaman publik (beranda, peta, artikel, kontak, auth)
| Prefix: /
| Middleware: —
|
*/

// ── Guest Pages (FR-PU-01 to FR-PU-04) ──
Route::get('/', [GuestController::class, 'home'])->name('home');
Route::get('/peta', [GuestController::class, 'map'])->name('map');
Route::get('/artikel', [GuestController::class, 'articles'])->name('articles');
Route::get('/artikel/{slug}', [GuestController::class, 'articleDetail'])->name('article.detail');
Route::get('/kontak', [GuestController::class, 'contact'])->name('contact');
Route::post('/kontak', [GuestController::class, 'submitContact'])->name('contact.submit');

// ── Auth (FR-PU-05) ──
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::get('/admin/login', [AuthController::class, 'showAdminLogin'])->name('admin.login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Lupa Password Routes
    Route::get('/lupa-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/lupa-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/verifikasi-otp', [AuthController::class, 'showVerifyOtp'])->name('password.verify.form');
    Route::post('/verifikasi-otp', [AuthController::class, 'verifyOtp'])->name('password.verify.otp');
    Route::get('/reset-password', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'processResetPassword'])->name('password.update');
});

Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── User Routes (FR-US-01 to FR-US-11) ──
Route::middleware(['auth', 'role:user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan/buat', [\App\Http\Controllers\User\UserController::class, 'createReport'])->name('report.create');
    Route::post('/laporan/buat', [\App\Http\Controllers\User\UserController::class, 'storeReport'])->name('report.store');
    Route::get('/laporan', [\App\Http\Controllers\User\UserController::class, 'reports'])->name('reports');
    Route::get('/laporan/{id}', [\App\Http\Controllers\User\UserController::class, 'reportDetail'])->name('report.detail');
    Route::post('/laporan/{id}/konfirmasi', [\App\Http\Controllers\User\UserController::class, 'confirmReport'])->name('report.confirm');
    Route::post('/laporan/{id}/ulasan', [\App\Http\Controllers\User\UserController::class, 'submitFeedback'])->name('report.feedback');
    Route::get('/profil', [\App\Http\Controllers\User\UserController::class, 'profile'])->name('profile');
    Route::put('/profil', [\App\Http\Controllers\User\UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [\App\Http\Controllers\User\UserController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifikasi', [\App\Http\Controllers\User\UserController::class, 'notifications'])->name('notifications');
    Route::get('/notifikasi/{id}/baca', [\App\Http\Controllers\User\UserController::class, 'readNotification'])->name('notification.read');
});

// ── Admin Routes (FR-AD-01 to FR-AD-16) ──
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/laporan', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports');
    Route::get('/laporan/export-pdf', [\App\Http\Controllers\Admin\ReportController::class, 'exportPdf'])->name('reports.export_pdf');
    Route::get('/laporan/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'show'])->name('reports.show');
    Route::put('/laporan/{id}/verifikasi', [\App\Http\Controllers\Admin\ReportController::class, 'verify'])->name('reports.verify');
    Route::get('/petugas', [\App\Http\Controllers\Admin\AdminController::class, 'officers'])->name('officers');
    Route::post('/petugas', [\App\Http\Controllers\Admin\AdminController::class, 'storeOfficer'])->name('officers.store');
    Route::put('/petugas/{id}', [\App\Http\Controllers\Admin\AdminController::class, 'updateOfficer'])->name('officers.update');
    Route::get('/profil', [\App\Http\Controllers\Admin\AdminController::class, 'profile'])->name('profile');
    
    // Master Data & Kotak Masuk
    Route::get('/wilayah', [\App\Http\Controllers\Admin\AdminDataController::class, 'districts'])->name('districts');
    Route::post('/wilayah', [\App\Http\Controllers\Admin\AdminDataController::class, 'storeDistrict'])->name('districts.store');
    Route::put('/wilayah/{id}', [\App\Http\Controllers\Admin\AdminDataController::class, 'updateDistrict'])->name('districts.update');
    Route::delete('/wilayah/{id}', [\App\Http\Controllers\Admin\AdminDataController::class, 'destroyDistrict'])->name('districts.destroy');

    Route::get('/kategori', [\App\Http\Controllers\Admin\AdminDataController::class, 'categories'])->name('categories');
    Route::post('/kategori', [\App\Http\Controllers\Admin\AdminDataController::class, 'storeCategory'])->name('categories.store');
    Route::put('/kategori/{id}', [\App\Http\Controllers\Admin\AdminDataController::class, 'updateCategory'])->name('categories.update');
    Route::delete('/kategori/{id}', [\App\Http\Controllers\Admin\AdminDataController::class, 'destroyCategory'])->name('categories.destroy');

    Route::get('/pesan-masuk', [\App\Http\Controllers\Admin\AdminDataController::class, 'messages'])->name('messages');
    Route::delete('/pesan-masuk/{id}', [\App\Http\Controllers\Admin\AdminDataController::class, 'destroyMessage'])->name('messages.destroy');

    // Artikel
    Route::resource('artikel', \App\Http\Controllers\Admin\ArticleController::class)->except(['show']);
});

Route::middleware(['auth', 'role:officer'])->prefix('officer')->name('officer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Officer\OfficerController::class, 'dashboard'])->name('dashboard');
    Route::get('/tugas', [\App\Http\Controllers\Officer\TaskController::class, 'index'])->name('tasks');
    Route::get('/tugas/{id}', [\App\Http\Controllers\Officer\TaskController::class, 'show'])->name('tasks.show');
    Route::put('/tugas/{id}/update', [\App\Http\Controllers\Officer\TaskController::class, 'update'])->name('tasks.update');
    Route::get('/profil', [\App\Http\Controllers\Officer\OfficerController::class, 'profile'])->name('profile');
    Route::put('/profil', [\App\Http\Controllers\Officer\OfficerController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profil/password', [\App\Http\Controllers\Officer\OfficerController::class, 'updatePassword'])->name('profile.password');
    Route::get('/notifikasi', [\App\Http\Controllers\Officer\OfficerController::class, 'notifications'])->name('notifications');
    Route::get('/notifikasi/{id}/baca', [\App\Http\Controllers\Officer\OfficerController::class, 'readNotification'])->name('notification.read');
});

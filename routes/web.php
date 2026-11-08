<?php

declare(strict_types=1);

use App\Exports\PpdbEmisExport;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminPpdbController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Parent\ParentDashboardController;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\Teacher\TeacherDashboardController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// 1. Public Landing Page
Route::get('/', [PublicController::class, 'index'])->name('home');

// 2. Modul PPDB Online (Publik)
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PpdbController::class, 'create'])->name('register');
    Route::post('/daftar', [PpdbController::class, 'store'])->name('store');
    Route::get('/sukses/{registration_number}', [PpdbController::class, 'success'])->name('success');
    Route::get('/cek-status', [PpdbController::class, 'checkStatus'])->name('check_status');
    Route::get('/cetak/{registration_number}', [PpdbController::class, 'downloadProof'])->name('print_proof');
});

// 3. Autentikasi Sistem Terpadu
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Shortcut URL Login Portal
Route::get('/admin/login', fn () => redirect()->route('login', ['portal' => 'admin']));
Route::get('/guru/login', fn () => redirect()->route('login', ['portal' => 'teacher']));
Route::get('/wali/login', fn () => redirect()->route('login', ['portal' => 'parent']));

// 4. Custom Portal Admin / TU
Route::prefix('admin')->middleware(['auth', 'role:admin'])->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/ppdb', [AdminPpdbController::class, 'index'])->name('ppdb.index');
    Route::get('/ppdb/{registration}', [AdminPpdbController::class, 'show'])->name('ppdb.show');
    Route::post('/ppdb/{registration}/status', [AdminPpdbController::class, 'updateStatus'])->name('ppdb.update_status');
    Route::get('/export/ppdb-emis', fn () => Excel::download(new PpdbEmisExport, 'data-ppdb-emis.xlsx'))->name('ppdb.export_emis');
});

// 5. Custom Portal Guru & Wali Kelas
Route::prefix('guru')->middleware(['auth', 'role:teacher'])->name('teacher.')->group(function () {
    Route::get('/', [TeacherDashboardController::class, 'index'])->name('dashboard');
});

// 6. Custom Portal Wali Murid
Route::prefix('wali')->middleware(['auth', 'role:parent'])->name('parent.')->group(function () {
    Route::get('/', [ParentDashboardController::class, 'index'])->name('dashboard');
});

<?php

declare(strict_types=1);

use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');

// Modul PPDB Online
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PpdbController::class, 'create'])->name('register');
    Route::post('/daftar', [PpdbController::class, 'store'])->name('store');
    Route::get('/sukses/{registration_number}', [PpdbController::class, 'success'])->name('success');
    Route::get('/cek-status', [PpdbController::class, 'checkStatus'])->name('check_status');
    Route::get('/cetak/{registration_number}', [PpdbController::class, 'downloadProof'])->name('print_proof');
});

<?php

declare(strict_types=1);

use App\Exports\PpdbEmisExport;
use App\Http\Controllers\PpdbController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', [PublicController::class, 'index'])->name('home');

// Modul PPDB Online (Publik)
Route::prefix('ppdb')->name('ppdb.')->group(function () {
    Route::get('/daftar', [PpdbController::class, 'create'])->name('register');
    Route::post('/daftar', [PpdbController::class, 'store'])->name('store');
    Route::get('/sukses/{registration_number}', [PpdbController::class, 'success'])->name('success');
    Route::get('/cek-status', [PpdbController::class, 'checkStatus'])->name('check_status');
    Route::get('/cetak/{registration_number}', [PpdbController::class, 'downloadProof'])->name('print_proof');
});

// Admin Export EMIS Kemenag
Route::get('/admin/export/ppdb-emis', function () {
    abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

    return Excel::download(new PpdbEmisExport, 'data-ppdb-emis.xlsx');
})->name('admin.ppdb.export_emis');

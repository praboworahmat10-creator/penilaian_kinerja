<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PerformanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');

Route::middleware('legacy.auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [PerformanceController::class, 'dashboard'])->name('dashboard');

    Route::get('/pegawai', [PerformanceController::class, 'employees'])->name('employees.index');
    Route::get('/pegawai/create', [PerformanceController::class, 'createEmployee'])->name('employees.create');
    Route::post('/pegawai', [PerformanceController::class, 'storeEmployee'])->name('employees.store');
    Route::get('/pegawai/{id}/edit', [PerformanceController::class, 'editEmployee'])
        ->whereNumber('id')->name('employees.edit');
    Route::put('/pegawai/{id}', [PerformanceController::class, 'updateEmployee'])
        ->whereNumber('id')->name('employees.update');
    Route::delete('/pegawai/{id}', [PerformanceController::class, 'destroyEmployee'])
        ->whereNumber('id')->name('employees.destroy');

    Route::get('/kategori-kinerja', [PerformanceController::class, 'categories'])
        ->name('performance.categories');
    Route::get('/penilaian-kinerja', [PerformanceController::class, 'index'])
        ->name('performance.index');
    Route::get('/penilaian-kinerja/create', [PerformanceController::class, 'create'])
        ->name('performance.create');
    Route::post('/penilaian-kinerja', [PerformanceController::class, 'store'])
        ->name('performance.store');
    Route::get('/penilaian-kinerja/{id}', [PerformanceController::class, 'show'])
        ->whereNumber('id')->name('performance.show');
    Route::delete('/penilaian-kinerja/{id}', [PerformanceController::class, 'destroy'])
        ->whereNumber('id')->name('performance.destroy');
});

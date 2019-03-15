<?php

use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\VersionController;


/*
 * All route names are prefixed with 'admin.'.
 */
Route::redirect('/', '/admin/dashboard', 301);
Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('version', [VersionController::class, 'index'])->name('version.index');
Route::get('version/create', [VersionController::class, 'create'])->name('version.create');
Route::post('version/store', [VersionController::class, 'store'])->name('version.store');
Route::get('version/edit/{id}', [VersionController::class, 'edit'])->name('version.edit');
Route::patch('version/update', [VersionController::class, 'update'])->name('version.update');
Route::get('version/verify/{id}', [VersionController::class, 'verify'])->name('version.verify');
Route::get('version/destroy/{id}', [VersionController::class, 'destroy'])->name('version.destroy');



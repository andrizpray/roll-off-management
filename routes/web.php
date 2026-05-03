<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefectItemController;
use App\Http\Controllers\RollItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');

Route::get('/items', [RollItemController::class, 'index'])->name('items.index');
Route::get('/items/create', [RollItemController::class, 'create'])->name('items.create');
Route::post('/items', [RollItemController::class, 'store'])->name('items.store');
Route::get('/items/export', [RollItemController::class, 'export'])->name('items.export');
Route::get('/items/import', [RollItemController::class, 'importForm'])->name('items.import');
Route::post('/items/import', [RollItemController::class, 'importPreview'])->name('items.import.preview');
Route::post('/items/import/sync', [RollItemController::class, 'importSync'])->name('items.import.sync');
Route::get('/items/{id}', [RollItemController::class, 'show'])->name('items.show');
Route::get('/items/{id}/edit', [RollItemController::class, 'edit'])->name('items.edit');
Route::put('/items/{id}', [RollItemController::class, 'update'])->name('items.update');
Route::delete('/items/{id}', [RollItemController::class, 'destroy'])->name('items.destroy');

Route::get('/defects', [DefectItemController::class, 'index'])->name('defects.index');
Route::get('/defects/export', [DefectItemController::class, 'export'])->name('defects.export');
Route::get('/defects/summary-report', [DefectItemController::class, 'summaryReport'])->name('defects.summary');

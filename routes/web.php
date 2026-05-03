<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefectItemController;
use App\Http\Controllers\RollItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/items', [RollItemController::class, 'index'])->name('items.index');
Route::get('/items/export', [RollItemController::class, 'export'])->name('items.export');
Route::get('/items/{id}', [RollItemController::class, 'show'])->name('items.show');

Route::get('/defects', [DefectItemController::class, 'index'])->name('defects.index');
Route::get('/defects/export', [DefectItemController::class, 'export'])->name('defects.export');
Route::get('/defects/summary-report', [DefectItemController::class, 'summaryReport'])->name('defects.summary');

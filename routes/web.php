<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DefectItemController;
use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\MobilController;
use App\Http\Controllers\RollItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
Route::get('/notifications/page', [DashboardController::class, 'notificationsPage'])->name('notifications.page');
Route::post('/notifications/mark-read', [DashboardController::class, 'markAsRead'])->name('notifications.mark-read');

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
Route::get('/defects/create', [DefectItemController::class, 'create'])->name('defects.create');
Route::post('/defects', [DefectItemController::class, 'store'])->name('defects.store');
Route::get('/defects/lookup', [DefectItemController::class, 'lookup'])->name('defects.lookup');
Route::get('/defects/export', [DefectItemController::class, 'export'])->name('defects.export');
Route::get('/defects/summary-report', [DefectItemController::class, 'summaryReport'])->name('defects.summary');
Route::get('/defects/import', [DefectItemController::class, 'importForm'])->name('defects.import');
Route::post('/defects/import', [DefectItemController::class, 'import'])->name('defects.import.post');
Route::get('/defects/import/template', [DefectItemController::class, 'importTemplate'])->name('defects.import.template');
Route::get('/defects/{id}/edit', [DefectItemController::class, 'edit'])->name('defects.edit');
Route::put('/defects/{id}', [DefectItemController::class, 'update'])->name('defects.update');
Route::delete('/defects/{id}', [DefectItemController::class, 'destroy'])->name('defects.destroy');

// ── Delivery Orders ──────────────────────────────────────────────────
Route::get('/delivery', [DeliveryOrderController::class, 'index'])->name('delivery.index');
Route::get('/delivery/create', [DeliveryOrderController::class, 'create'])->name('delivery.create');
Route::post('/delivery', [DeliveryOrderController::class, 'store'])->name('delivery.store');
Route::get('/delivery/{id}', [DeliveryOrderController::class, 'show'])->name('delivery.show');
Route::get('/delivery/{id}/edit', [DeliveryOrderController::class, 'edit'])->name('delivery.edit');
Route::put('/delivery/{id}', [DeliveryOrderController::class, 'update'])->name('delivery.update');
Route::delete('/delivery/{id}', [DeliveryOrderController::class, 'destroy'])->name('delivery.destroy');
Route::post('/delivery/{id}/confirm', [DeliveryOrderController::class, 'confirm'])->name('delivery.confirm');
Route::post('/delivery/{id}/assign', [DeliveryOrderController::class, 'assign'])->name('delivery.assign');
Route::post('/delivery/{id}/delivered', [DeliveryOrderController::class, 'delivered'])->name('delivery.delivered');
Route::get('/delivery/{id}/manifest', [DeliveryOrderController::class, 'exportManifest'])->name('delivery.manifest');
Route::get('/api/lot-lookup', [DeliveryOrderController::class, 'lotLookup'])->name('api.lot-lookup');

// ── Mobil ──────────────────────────────────────────────────────────────
Route::get('/mobil', [MobilController::class, 'index'])->name('mobil.index');
Route::get('/mobil/{mobilId}', [MobilController::class, 'show'])->name('mobil.show');
Route::delete('/mobil/{mobilId}/do/{doId}', [MobilController::class, 'removeDo'])->name('mobil.remove-do');

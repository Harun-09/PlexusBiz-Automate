<?php

use App\Http\Controllers\Admin\AdminBulkPricingController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSupplierController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'admin'])->name('dashboard');
    Route::get('/customers', [WorkspaceController::class, 'customers'])->name('customers.index');
    Route::get('/audit-logs', [WorkspaceController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/bulk-pricing', [AdminBulkPricingController::class, 'index'])->name('bulk-pricing.index');
    Route::put('/bulk-pricing/{product}', [AdminBulkPricingController::class, 'update'])->name('bulk-pricing.update');
    Route::post('/bulk-pricing/{product}/tiers', [AdminBulkPricingController::class, 'storeTier'])->name('bulk-pricing.tiers.store');
    Route::put('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'updateTier'])->name('bulk-pricing.tiers.update');
    Route::delete('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'destroyTier'])->name('bulk-pricing.tiers.destroy');

    Route::resource('users', AdminUserController::class)->except('show');
    Route::resource('suppliers', AdminSupplierController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
});

<?php

use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSupplierController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'admin'])->name('dashboard');
    Route::get('/customers', [WorkspaceController::class, 'customers'])->name('customers.index');

    Route::resource('users', AdminUserController::class)->except('show');
    Route::resource('suppliers', AdminSupplierController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
});

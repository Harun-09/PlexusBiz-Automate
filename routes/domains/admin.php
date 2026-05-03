<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'admin'])->name('dashboard');
    Route::get('/users', [WorkspaceController::class, 'users'])->name('users.index');
    Route::get('/customers', [WorkspaceController::class, 'customers'])->name('customers.index');
});

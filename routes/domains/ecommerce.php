<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('commerce')->name('commerce.')->group(function (): void {
    Route::get('/products', [WorkspaceController::class, 'supplierProducts'])
        ->middleware('role:supplier|admin')
        ->name('products.index');

    Route::get('/orders', [WorkspaceController::class, 'commerceOrders'])
        ->middleware('role:buyer|supplier|admin')
        ->name('orders.index');
});

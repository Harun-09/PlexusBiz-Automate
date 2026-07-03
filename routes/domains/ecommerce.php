<?php

use App\Http\Controllers\WorkspaceController;
use App\Http\Controllers\RfqResponseController;
use Illuminate\Support\Facades\Route;

Route::prefix('commerce')->name('commerce.')->group(function (): void {
    Route::get('/products', [WorkspaceController::class, 'supplierProducts'])
        ->middleware('role:supplier|admin')
        ->name('products.index');

    Route::middleware('role:supplier')->group(function (): void {
        Route::get('/products/create', [WorkspaceController::class, 'supplierProductCreate'])
            ->name('products.create');

        Route::post('/products', [WorkspaceController::class, 'supplierProductStore'])
            ->name('products.store');

        Route::get('/products/{product}/edit', [WorkspaceController::class, 'supplierProductEdit'])
            ->name('products.edit');

        Route::put('/products/{product}', [WorkspaceController::class, 'supplierProductUpdate'])
            ->name('products.update');

        Route::delete('/products/{product}', [WorkspaceController::class, 'supplierProductDestroy'])
            ->name('products.destroy');
    });

    Route::get('/orders', [WorkspaceController::class, 'commerceOrders'])
        ->middleware('role:buyer|supplier|admin')
        ->name('orders.index');

    Route::post('/orders/{order}/mark-delivered', [WorkspaceController::class, 'markOrderDelivered'])
        ->middleware('role:buyer|admin')
        ->name('orders.mark-delivered');

    Route::get('/rfq-responses', [WorkspaceController::class, 'supplierRfqResponses'])
        ->middleware('role:supplier|admin')
        ->name('rfq-responses.index');

    Route::get('/rfq-quotes', [WorkspaceController::class, 'buyerRfqResponses'])
        ->middleware('role:buyer|admin')
        ->name('rfq-quotes.index');

    Route::get('/rfq-responses/{rfq}/create', [RfqResponseController::class, 'create'])
        ->middleware('role:supplier|admin')
        ->name('rfq-responses.create');

    Route::post('/rfq-responses/{rfq}', [RfqResponseController::class, 'store'])
        ->middleware('role:supplier|admin')
        ->name('rfq-responses.store');

    Route::post('/rfq-responses/{rfqResponse}/accept', [RfqResponseController::class, 'accept'])
        ->middleware('role:buyer|admin')
        ->name('rfq-responses.accept');

    Route::post('/rfq-responses/{rfqResponse}/reject', [RfqResponseController::class, 'reject'])
        ->middleware('role:buyer|admin')
        ->name('rfq-responses.reject');

    Route::get('/supplier-orders', [WorkspaceController::class, 'supplierOrders'])
        ->middleware('role:supplier|admin')
        ->name('supplier-orders.index');

    Route::post('/supplier-orders/{supplierOrder}/status/{status}', [WorkspaceController::class, 'supplierOrderStatusUpdate'])
        ->middleware('role:supplier|admin')
        ->name('supplier-orders.status');
});

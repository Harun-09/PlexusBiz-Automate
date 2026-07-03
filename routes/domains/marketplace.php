<?php

use App\Http\Controllers\Marketplace\CartController;
use App\Http\Controllers\Marketplace\CheckoutController;
use App\Http\Controllers\Marketplace\ProductController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/bulk-orders', [ProductController::class, 'index'])
    ->name('products.bulk');

Route::get('/products/moq-pricing', [ProductController::class, 'index'])
    ->name('products.moq');

Route::get('/products/{slug}', [ProductController::class, 'show'])
    ->name('products.show');

Route::middleware(['auth:web,b2c'])->group(function (): void {
    Route::get('/cart', [CartController::class, 'index'])
        ->name('cart.index');

    Route::post('/cart/add', [CartController::class, 'add'])
        ->name('cart.add');

    Route::post('/cart/update', [CartController::class, 'update'])
        ->name('cart.update');

    Route::post('/cart/remove', [CartController::class, 'remove'])
        ->name('cart.remove');

    Route::get('/checkout', [CheckoutController::class, 'index'])
        ->name('checkout.index');

    Route::post('/checkout', [CheckoutController::class, 'process'])
        ->name('checkout.process');
});

Route::prefix('marketplace')->name('marketplace.')->middleware('auth')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'marketplace'])->name('index');
});

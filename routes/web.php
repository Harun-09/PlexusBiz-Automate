<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/p/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

Route::redirect('/auth/login', '/login');

Route::get('/checkout/success/{orderNumber}', [PaymentController::class, 'checkoutSuccess'])
    ->name('checkout.success');

Route::middleware('auth')->group(function () {
    Route::post('/checkout/{orderNumber}/payment', [PaymentController::class, 'process'])
        ->name('payment.process');
});

Route::prefix('payments')->name('payment.')->group(function (): void {
    Route::get('/stripe/{orderNumber}/success', [PaymentController::class, 'stripeSuccess'])
        ->name('stripe.success');

    Route::get('/stripe/{orderNumber}/cancel', [PaymentController::class, 'stripeCancel'])
        ->name('stripe.cancel');

    Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])
        ->name('stripe.webhook');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/success', [PaymentController::class, 'sslcommerzSuccess'])
        ->name('sslcommerz.success');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/fail', [PaymentController::class, 'sslcommerzFail'])
        ->name('sslcommerz.fail');

    Route::match(['get', 'post'], '/sslcommerz/{orderNumber}/cancel', [PaymentController::class, 'sslcommerzCancel'])
        ->name('sslcommerz.cancel');

    Route::post('/sslcommerz/ipn', [PaymentController::class, 'sslcommerzIPN'])
        ->name('sslcommerz.ipn');
});

Route::get('/dashboard', DashboardController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Invoice Routes
    Route::prefix('invoices')->name('invoices.')->group(function (): void {
        Route::get('/', [InvoiceController::class, 'index'])->name('index');
        Route::post('/generate/{orderId}', [InvoiceController::class, 'generate'])->name('generate');
        Route::get('/{invoice}', [InvoiceController::class, 'show'])->name('show');
        Route::get('/{invoice}/download', [InvoiceController::class, 'download'])->name('download');
        Route::get('/{invoice}/preview', [InvoiceController::class, 'stream'])->name('preview');
    });
});

require __DIR__.'/auth.php';

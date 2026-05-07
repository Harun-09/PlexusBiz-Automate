<?php

use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PublicPageController;
use App\Http\Controllers\RfqRequestController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RouteAliasController;
use App\Http\Controllers\SupplierOnboardingController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', WelcomeController::class)->name('landing');

Route::get('/about', [PublicPageController::class, 'about'])->name('about');
Route::get('/contact', [PublicPageController::class, 'contact'])->name('contact');
Route::get('/terms', [PublicPageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PublicPageController::class, 'privacy'])->name('privacy');
Route::get('/faq', [PublicPageController::class, 'faq'])->name('faq');

Route::get('/p/{slug}', [App\Http\Controllers\PageController::class, 'show'])->name('page.show');

Route::get('/auth/login', [RouteAliasController::class, 'authLogin']);

Route::middleware('guest')->group(function (): void {
    Route::get('/supplier/apply', [SupplierOnboardingController::class, 'create'])
        ->name('supplier.apply');

    Route::post('/supplier/apply', [SupplierOnboardingController::class, 'store'])
        ->name('supplier.apply.store');

    Route::get('/register-supplier', [SupplierOnboardingController::class, 'create'])
        ->name('supplier.register');

    Route::post('/register-supplier', [SupplierOnboardingController::class, 'store'])
        ->name('supplier.register.store');
});

Route::get('/checkout/success/{orderNumber}', [PaymentController::class, 'checkoutSuccess'])
    ->name('checkout.success');

Route::get('/rfq', [RfqRequestController::class, 'create'])
    ->name('rfq.create');

Route::get('/rfq/{product:slug}', [RfqRequestController::class, 'create'])
    ->name('rfq.product');

Route::post('/rfq', [RfqRequestController::class, 'store'])
    ->name('rfq.store');

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
    Route::get('/customer/profile', [RouteAliasController::class, 'customerProfileAlias'])->name('customer.profile.alias');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profile/customer', [ProfileController::class, 'updateCustomer'])->name('profile.customer.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('supplier')->name('supplier.')->middleware('role:supplier|admin')->group(function (): void {
        Route::get('/products', [WorkspaceController::class, 'supplierProducts'])->name('products.index');
        Route::get('/products/create', [WorkspaceController::class, 'supplierProductCreate'])->middleware('role:supplier')->name('products.create');
        Route::get('/products/{product}/edit', [WorkspaceController::class, 'supplierProductEdit'])->middleware('role:supplier')->name('products.edit');
    });

    Route::get('/buyer/tickets', [RouteAliasController::class, 'buyerTicketsAlias'])->name('buyer.tickets.alias');

    Route::get('/orders', [RouteAliasController::class, 'ordersIndexAlias'])->name('orders.index.alias');
    Route::get('/orders/{order}', [RouteAliasController::class, 'ordersShowAlias'])
        ->middleware('role:buyer|supplier|admin')
        ->name('orders.show.alias');

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

<?php

use App\Http\Controllers\Crm\CrmController;
use Illuminate\Support\Facades\Route;

Route::prefix('crm')
    ->name('crm.')
    ->middleware('role:admin|marketing_manager')
    ->group(function (): void {
        Route::get('/', [CrmController::class, 'index'])->name('index');

        Route::get('/customers', [CrmController::class, 'customers'])->name('customers.index');
        Route::get('/customers/{customer}', [CrmController::class, 'show'])->name('customers.show');

        Route::get('/purchases', [CrmController::class, 'purchases'])->name('purchases.index');
        Route::get('/segments', [CrmController::class, 'segments'])->name('segments.index');
        Route::get('/leads', [CrmController::class, 'leads'])->name('leads.index');
        Route::get('/interactions', [CrmController::class, 'interactions'])->name('interactions.index');
    });

<?php

use App\Http\Controllers\Crm\CrmController;
use App\Http\Controllers\Crm\LeadController;
use Illuminate\Support\Facades\Route;

Route::prefix('crm')
    ->name('crm.')
    ->middleware('role:admin|marketing_manager')
    ->group(function (): void {
        Route::get('/', [CrmController::class, 'index'])->name('index');

        Route::get('/customers', [CrmController::class, 'customers'])->name('customers.index');
        Route::get('/customers/{customer}', [CrmController::class, 'show'])->name('customers.show');
        Route::get('/customers/{customer}/edit', [CrmController::class, 'edit'])->name('customers.edit');
        Route::put('/customers/{customer}', [CrmController::class, 'update'])->name('customers.update');

        Route::get('/purchases', [CrmController::class, 'purchases'])->name('purchases.index');
        Route::get('/segments', [CrmController::class, 'segments'])->name('segments.index');
        Route::get('/leads', [CrmController::class, 'leads'])->name('leads.index');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
        Route::put('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update');
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
        Route::get('/interactions', [CrmController::class, 'interactions'])->name('interactions.index');
    });

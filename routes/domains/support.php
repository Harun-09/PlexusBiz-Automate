<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('support')->name('support.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('support.tickets.index'))->name('index');

    Route::get('/tickets', [WorkspaceController::class, 'supportTickets'])
        ->middleware('role:buyer|supplier|admin')
        ->name('tickets.index');

    Route::get('/faq', [WorkspaceController::class, 'supportFaqs'])
        ->middleware('auth')
        ->name('faq.index');
});

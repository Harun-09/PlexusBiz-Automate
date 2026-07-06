<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('support')->name('support.')->group(function (): void {
    Route::get('/', fn () => redirect()->route('support.tickets.index'))->name('index');

    Route::middleware(['auth', 'role:buyer|supplier|admin'])->group(function (): void {
        Route::get('/tickets', [WorkspaceController::class, 'supportTickets'])->name('tickets.index');
        Route::get('/tickets/create', [WorkspaceController::class, 'supportTicketCreate'])->name('tickets.create');
        Route::post('/tickets', [WorkspaceController::class, 'supportTicketStore'])->name('tickets.store');
        Route::get('/tickets/{supportTicket}', [WorkspaceController::class, 'supportTicketShow'])->name('tickets.show');
        Route::post('/tickets/{supportTicket}/reply', [WorkspaceController::class, 'supportTicketReply'])->name('tickets.reply');
        Route::put('/tickets/{supportTicket}/status', [WorkspaceController::class, 'supportTicketStatus'])->name('tickets.status');
        Route::put('/tickets/{supportTicket}/assign', [WorkspaceController::class, 'supportTicketAssign'])->name('tickets.assign');

        // Help Center Routes
        Route::get('/help-center', [WorkspaceController::class, 'supportHelpCenter'])->name('help-center');
        Route::post('/help-center/message', [WorkspaceController::class, 'supportChatbotMessage'])->name('help-center.message');
        Route::post('/help-center/email-escalate', [WorkspaceController::class, 'supportEmailEscalate'])->name('help-center.email-escalate');
    });

    Route::get('/faq', [WorkspaceController::class, 'supportFaqs'])
        ->middleware('auth')
        ->name('faq.index');
});

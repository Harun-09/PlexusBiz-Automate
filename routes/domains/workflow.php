<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('workflow')->name('workflow.')->middleware('role:workflow_manager|marketing_manager|admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('workflow.rules.index'))->name('index');

    Route::get('/rules', [WorkspaceController::class, 'workflowRules'])->name('rules.index');
    Route::get('/logs', [WorkspaceController::class, 'workflowLogs'])->name('logs.index');
});

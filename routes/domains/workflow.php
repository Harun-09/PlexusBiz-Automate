<?php

use App\Http\Controllers\Workflow\AutomationRuleController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('workflow')->name('workflow.')->middleware('role:workflow_manager|marketing_manager|admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('workflow.rules.index'))->name('index');

    Route::get('/rules', [WorkspaceController::class, 'workflowRules'])->name('rules.index');
    Route::get('/rules/create', [AutomationRuleController::class, 'create'])->name('rules.create');
    Route::post('/rules', [AutomationRuleController::class, 'store'])->name('rules.store');
    Route::get('/rules/{rule}/edit', [AutomationRuleController::class, 'edit'])->name('rules.edit');
    Route::put('/rules/{rule}', [AutomationRuleController::class, 'update'])->name('rules.update');
    Route::delete('/rules/{rule}', [AutomationRuleController::class, 'destroy'])->name('rules.destroy');
    Route::get('/logs', [WorkspaceController::class, 'workflowLogs'])->name('logs.index');
});

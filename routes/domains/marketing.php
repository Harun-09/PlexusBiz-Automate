<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('marketing')->name('marketing.')->middleware('role:marketing_manager|admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('marketing.campaigns.index'))->name('index');

    Route::get('/campaigns', [WorkspaceController::class, 'campaigns'])->name('campaigns.index');
    Route::get('/templates', [WorkspaceController::class, 'campaignTemplates'])->name('templates.index');
});

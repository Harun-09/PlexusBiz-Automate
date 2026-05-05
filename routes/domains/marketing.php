<?php

use App\Http\Controllers\Marketing\CampaignController;
use App\Http\Controllers\Marketing\CampaignTemplateController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('marketing')->name('marketing.')->middleware('role:marketing_manager|admin')->group(function (): void {
    Route::redirect('/', '/marketing/campaigns')->name('index');

    Route::get('/campaigns', [WorkspaceController::class, 'campaigns'])->name('campaigns.index');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    Route::get('/templates', [WorkspaceController::class, 'campaignTemplates'])->name('templates.index');
    Route::get('/templates/create', [CampaignTemplateController::class, 'create'])->name('templates.create');
    Route::post('/templates', [CampaignTemplateController::class, 'store'])->name('templates.store');
    Route::get('/templates/{campaignTemplate}/edit', [CampaignTemplateController::class, 'edit'])->name('templates.edit');
    Route::put('/templates/{campaignTemplate}', [CampaignTemplateController::class, 'update'])->name('templates.update');
    Route::delete('/templates/{campaignTemplate}', [CampaignTemplateController::class, 'destroy'])->name('templates.destroy');
});

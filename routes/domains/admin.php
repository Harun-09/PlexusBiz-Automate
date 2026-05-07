<?php

use App\Http\Controllers\Admin\AdminBulkPricingController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSupplierController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\RouteAliasController;
use App\Http\Controllers\Settings\ModuleSettingsController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'admin'])->name('dashboard');
    Route::get('/customers', [WorkspaceController::class, 'customers'])->name('customers.index');
    Route::get('/audit-logs', [WorkspaceController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/modules', [ModuleSettingsController::class, 'index'])->name('modules.index');
    Route::patch('/modules', [ModuleSettingsController::class, 'update'])->name('modules.update');
    Route::get('/leads', [RouteAliasController::class, 'adminLeadsIndexAlias'])->name('leads.index.alias');
    Route::get('/leads/create', [RouteAliasController::class, 'adminLeadsCreateAlias'])->name('leads.create.alias');
    Route::get('/campaigns', [RouteAliasController::class, 'adminCampaignsIndexAlias'])->name('campaigns.index.alias');
    Route::get('/campaigns/create', [RouteAliasController::class, 'adminCampaignsCreateAlias'])->name('campaigns.create.alias');
    Route::get('/campaign-templates', [RouteAliasController::class, 'adminTemplatesIndexAlias'])->name('templates.index.alias');
    Route::get('/social-posts', [RouteAliasController::class, 'adminSocialPostsIndexAlias'])->name('social-posts.index.alias');
    Route::get('/social-posts/create', [RouteAliasController::class, 'adminSocialPostsCreateAlias'])->name('social-posts.create.alias');
    Route::get('/social-calendar', [RouteAliasController::class, 'adminSocialCalendarAlias'])->name('social-calendar.alias');
    Route::get('/automation-rules', [RouteAliasController::class, 'adminAutomationRulesIndexAlias'])->name('automation-rules.index.alias');
    Route::get('/automation-rules/create', [RouteAliasController::class, 'adminAutomationRulesCreateAlias'])->name('automation-rules.create.alias');
    Route::get('/workflow-logs', [RouteAliasController::class, 'adminWorkflowLogsAlias'])->name('workflow-logs.alias');
    Route::get('/tickets', [RouteAliasController::class, 'adminTicketsIndexAlias'])->name('tickets.index.alias');
    Route::get('/bulk-pricing', [AdminBulkPricingController::class, 'index'])->name('bulk-pricing.index');
    Route::put('/bulk-pricing/{product}', [AdminBulkPricingController::class, 'update'])->name('bulk-pricing.update');
    Route::post('/bulk-pricing/{product}/tiers', [AdminBulkPricingController::class, 'storeTier'])->name('bulk-pricing.tiers.store');
    Route::put('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'updateTier'])->name('bulk-pricing.tiers.update');
    Route::delete('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'destroyTier'])->name('bulk-pricing.tiers.destroy');

    Route::resource('users', AdminUserController::class)->except('show');
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::resource('suppliers', AdminSupplierController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
});

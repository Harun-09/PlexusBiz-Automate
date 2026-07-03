<?php

use App\Http\Controllers\Admin\AdminBulkPricingController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSupplierController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Settings\ModuleSettingsController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'admin'])->name('dashboard');
    Route::get('/customers', [WorkspaceController::class, 'customers'])->name('customers.index');
    Route::get('/audit-logs', [WorkspaceController::class, 'auditLogs'])->name('audit-logs');
    Route::get('/modules', [ModuleSettingsController::class, 'index'])->name('modules.index');
    Route::patch('/modules', [ModuleSettingsController::class, 'update'])->name('modules.update');
    Route::redirect('/leads', '/crm/leads')->name('leads.index.alias');
    Route::redirect('/leads/create', '/crm/leads/create')->name('leads.create.alias');
    Route::redirect('/campaigns', '/marketing/campaigns')->name('campaigns.index.alias');
    Route::redirect('/campaigns/create', '/marketing/campaigns/create')->name('campaigns.create.alias');
    Route::redirect('/campaign-templates', '/marketing/templates')->name('templates.index.alias');
    Route::redirect('/social-posts', '/social/posts')->name('social-posts.index.alias');
    Route::redirect('/social-posts/create', '/social/posts/create')->name('social-posts.create.alias');
    Route::redirect('/social-calendar', '/social/calendar')->name('social-calendar.alias');
    Route::redirect('/automation-rules', '/workflow/rules')->name('automation-rules.index.alias');
    Route::redirect('/automation-rules/create', '/workflow/rules/create')->name('automation-rules.create.alias');
    Route::redirect('/workflow-logs', '/workflow/logs')->name('workflow-logs.alias');
    Route::redirect('/tickets', '/support/tickets')->name('tickets.index.alias');
    Route::get('/bulk-pricing', [AdminBulkPricingController::class, 'index'])->name('bulk-pricing.index');
    Route::put('/bulk-pricing/{product}', [AdminBulkPricingController::class, 'update'])->name('bulk-pricing.update');
    Route::post('/bulk-pricing/{product}/tiers', [AdminBulkPricingController::class, 'storeTier'])->name('bulk-pricing.tiers.store');
    Route::put('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'updateTier'])->name('bulk-pricing.tiers.update');
    Route::delete('/bulk-pricing/{product}/tiers/{tier}', [AdminBulkPricingController::class, 'destroyTier'])->name('bulk-pricing.tiers.destroy');

    Route::get('/trash', [\App\Http\Controllers\Admin\AdminTrashController::class, 'index'])->name('trash.index');
    Route::post('/trash/products/{id}/restore', [\App\Http\Controllers\Admin\AdminTrashController::class, 'restoreProduct'])->name('trash.products.restore');
    Route::post('/trash/orders/{id}/restore', [\App\Http\Controllers\Admin\AdminTrashController::class, 'restoreOrder'])->name('trash.orders.restore');
    Route::post('/trash/users/{id}/restore', [\App\Http\Controllers\Admin\AdminTrashController::class, 'restoreUser'])->name('trash.users.restore');
    Route::post('/trash/suppliers/{id}/restore', [\App\Http\Controllers\Admin\AdminTrashController::class, 'restoreSupplier'])->name('trash.suppliers.restore');

    Route::resource('users', AdminUserController::class)->except('show');
    Route::patch('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
    Route::patch('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
    Route::resource('suppliers', AdminSupplierController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');
});

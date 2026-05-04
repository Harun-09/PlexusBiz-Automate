<?php

use App\Http\Controllers\Settings\ModuleSettingsController;
use Illuminate\Support\Facades\Route;

Route::prefix('settings')->name('settings.')->group(function (): void {
    Route::get('/modules', [ModuleSettingsController::class, 'index'])->middleware('role:admin')->name('modules.index');
    Route::patch('/modules', [ModuleSettingsController::class, 'update'])->middleware('role:admin')->name('modules.update');
});

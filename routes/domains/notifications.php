<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->name('notifications.')->middleware('auth')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'notifications'])->name('index');
});

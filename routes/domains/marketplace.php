<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('marketplace')->name('marketplace.')->middleware('auth')->group(function (): void {
    Route::get('/', [WorkspaceController::class, 'marketplace'])->name('index');
});

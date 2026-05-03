<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('social')->name('social.')->middleware('role:marketing_manager|admin')->group(function (): void {
    Route::get('/calendar', [WorkspaceController::class, 'socialCalendar'])->name('calendar');
});

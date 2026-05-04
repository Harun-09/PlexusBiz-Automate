<?php

use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('social')->name('social.')->middleware('role:marketing_manager|admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('social.calendar'))->name('index');

    Route::get('/calendar', [WorkspaceController::class, 'socialCalendar'])->name('calendar');
    Route::get('/posts', [WorkspaceController::class, 'socialPosts'])->name('posts.index');
    Route::get('/accounts', [WorkspaceController::class, 'socialAccounts'])->name('accounts.index');
});

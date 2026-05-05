<?php

use App\Http\Controllers\Social\SocialPostController;
use App\Http\Controllers\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::prefix('social')->name('social.')->middleware('role:marketing_manager|admin')->group(function (): void {
    Route::get('/', fn () => redirect()->route('social.calendar'))->name('index');

    Route::get('/calendar', [WorkspaceController::class, 'socialCalendar'])->name('calendar');
    Route::get('/posts', [WorkspaceController::class, 'socialPosts'])->name('posts.index');
    Route::get('/posts/create', [SocialPostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [SocialPostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{socialPost}/edit', [SocialPostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{socialPost}', [SocialPostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{socialPost}', [SocialPostController::class, 'destroy'])->name('posts.destroy');
    Route::get('/accounts', [WorkspaceController::class, 'socialAccounts'])->name('accounts.index');
});

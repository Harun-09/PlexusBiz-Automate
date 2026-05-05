<?php

use App\Http\Controllers\Social\SocialAccountController;
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
    Route::get('/accounts/create', [SocialAccountController::class, 'create'])->name('accounts.create');
    Route::post('/accounts', [SocialAccountController::class, 'store'])->name('accounts.store');
    Route::get('/accounts/{socialAccount}/edit', [SocialAccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/accounts/{socialAccount}', [SocialAccountController::class, 'update'])->name('accounts.update');
    Route::delete('/accounts/{socialAccount}', [SocialAccountController::class, 'destroy'])->name('accounts.destroy');
});

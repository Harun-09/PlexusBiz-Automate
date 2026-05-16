<?php

use App\Http\Controllers\Api\SupportChatbotController;
use App\Http\Controllers\Api\UserProfileController;
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\CampaignTemplateController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SocialPostController;
use App\Http\Controllers\Api\V1\SupportTicketController;
use App\Http\Controllers\Api\V1\WorkflowLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', UserProfileController::class);

Route::middleware('auth:sanctum')->prefix('support')->name('support.')->group(function (): void {
    Route::post('/chatbot/message', SupportChatbotController::class)->name('chatbot.message');
});

Route::middleware('auth:sanctum')->prefix('v1')->name('v1.')->group(function (): void {
    Route::apiResource('products', ProductController::class);
    Route::apiResource('orders', OrderController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('campaigns', CampaignController::class);
    Route::apiResource('campaign-templates', CampaignTemplateController::class);
    Route::apiResource('social-posts', SocialPostController::class);
    Route::apiResource('workflow-logs', WorkflowLogController::class);
    Route::post('/support/chatbot/message', SupportChatbotController::class)->name('support.chatbot.message');
    Route::apiResource('support-tickets', SupportTicketController::class)->only(['index', 'show', 'store']);
    Route::post('/support-tickets/{supportTicket}/reply', [SupportTicketController::class, 'reply'])->name('support-tickets.reply');
    Route::put('/support-tickets/{supportTicket}/status', [SupportTicketController::class, 'updateStatus'])->name('support-tickets.status');
    Route::put('/support-tickets/{supportTicket}/assign', [SupportTicketController::class, 'assign'])->name('support-tickets.assign');

    // Messages API
    Route::get('/messages/unread-count', [MessageController::class, 'unreadCount'])->name('messages.unread-count');
    Route::get('/messages/recent', [MessageController::class, 'recent'])->name('messages.recent');
    Route::apiResource('messages', MessageController::class)->only(['index', 'store', 'show']);
    Route::post('/messages/{message}/read', [MessageController::class, 'markAsRead'])->name('messages.mark-as-read');

    Route::prefix('notifications')->name('notifications.')->group(function (): void {
        Route::get('/', [MessageController::class, 'index'])->name('index');
        Route::get('/unread-count', [MessageController::class, 'unreadCount'])->name('unread-count');
        Route::get('/recent', [MessageController::class, 'recent'])->name('recent');
        Route::put('/{message}/read', [MessageController::class, 'markAsRead'])->name('mark-as-read');
        Route::put('/read-all', [MessageController::class, 'markAllAsRead'])->name('read-all');
    });
});

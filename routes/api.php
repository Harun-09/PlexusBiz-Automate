<?php

use App\Http\Controllers\Api\SupportChatbotController;
use App\Http\Controllers\Api\V1\CampaignController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\SocialPostController;
use App\Http\Controllers\Api\V1\SupportTicketController;
use App\Http\Controllers\Api\V1\WorkflowLogController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->prefix('support')->name('support.')->group(function (): void {
    Route::post('/chatbot/message', SupportChatbotController::class)->name('chatbot.message');
});

Route::middleware('auth:sanctum')->prefix('v1')->name('v1.')->group(function (): void {
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('orders', OrderController::class)->only(['index', 'show']);
    Route::apiResource('customers', CustomerController::class)->only(['index', 'show']);
    Route::apiResource('campaigns', CampaignController::class)->only(['index', 'show']);
    Route::apiResource('social-posts', SocialPostController::class)->only(['index', 'show']);
    Route::apiResource('workflow-logs', WorkflowLogController::class)->only(['index', 'show']);
    Route::apiResource('support-tickets', SupportTicketController::class)->only(['index', 'show']);
    Route::post('/support/chatbot/message', SupportChatbotController::class)->name('support.chatbot.message');
});

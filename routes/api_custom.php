<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ConnectionApiController;
use App\Http\Controllers\WebhookController;

Route::post('/api/webhook/session-status', [WebhookController::class, 'handleSessionStatus']);
Route::get('/api/connection/status/{public_token}', [ConnectionApiController::class, 'externalStatus']);
Route::post('/api/connection/send-message/{public_token}', [ConnectionApiController::class, 'sendTextMessage']); 
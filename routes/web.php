<?php

use App\Events\HelloWorldEvent;
use App\Http\Controllers\ConnectionController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\WebhookController;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/trigger-hello', function () {
    event(new HelloWorldEvent());
    return 'Evento disparado!';
});

Route::post('/api/webhook/session-status', [WebhookController::class, 'handleSessionStatus']);
Route::get('/webhook/default', [WebhookController::class, 'webhookDefault']);

Route::get('/connections', [ConnectionController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('connections');
Route::get('/connection/enable-webhook/{connection}', [ConnectionController::class, 'toggleWebhookStatus'])
    ->middleware(['auth', 'verified'])
    ->name('connection.toggleWebhookStatus');
Route::post('/connections', [ConnectionController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('connections.store');
Route::get('/connections/status/{public_token}', [ConnectionController::class, 'status'])
    ->middleware(['auth', 'verified'])
    ->name('connections.status');
Route::get('/connections/profile/{public_token}', [ConnectionController::class, 'profile'])
    ->middleware(['auth', 'verified'])
    ->name('connections.profile');
Route::put('/connections/{connection}', [ConnectionController::class, 'update'])
    ->middleware(['auth', 'verified'])
    ->name('connections.update');
Route::post('/connections/{connection}/disconnect', [ConnectionController::class, 'disconnect'])
    ->middleware(['auth', 'verified'])
    ->name('connections.disconnect');
Route::delete('/connections/{connection}', [ConnectionController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('connections.destroy');
Route::post('/connections/{connection}/test-message', [ConnectionController::class, 'sendTestMessage'])
    ->middleware(['auth', 'verified'])
    ->name('connections.test-message');
Route::get('/connections/chart-data', [ConnectionController::class, 'getConnectionsChartData'])
    ->middleware(['auth', 'verified'])
    ->name('connections.chart-data');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';

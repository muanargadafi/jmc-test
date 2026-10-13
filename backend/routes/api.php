<?php

use App\Http\Controllers\Api\V1\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Health Check Routes
|--------------------------------------------------------------------------
| These routes have no version prefix and require no authentication—used
| by load balancers, uptime monitors, and readiness probes.
*/
Route::prefix('health')->group(function () {
    Route::get('/', [HealthController::class, 'ready']);
    Route::get('ready', [HealthController::class, 'ready']);
    Route::get('live', [HealthController::class, 'live']);
});

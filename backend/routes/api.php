<?php

use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\UserController;
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


/*
|--------------------------------------------------------------------------
| API v1
|--------------------------------------------------------------------------
*/
Route::prefix('v1')->group(function () {
    /*
    |----------------------------------------------------------------
    | Protected routes
    |----------------------------------------------------------------
    */
    Route::get('roles', [RoleController::class, 'index']);
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json(['message' => 'Not Found'], 404);
});

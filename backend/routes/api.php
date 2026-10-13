<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\HealthController;
use App\Http\Controllers\Api\V1\RoleController;
use App\Http\Controllers\Api\V1\SubCategoryController;
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
    | Auth (public)
    |----------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:login');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('me', [AuthController::class, 'me']);
        });
    });

    /*
    |----------------------------------------------------------------
    | Protected routes
    |----------------------------------------------------------------
    */

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
        Route::get('roles', [RoleController::class, 'index']);

        Route::prefix('users')->controller(UserController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{user}', 'show');
            Route::post('/', 'store');
            Route::put('/{user}', 'update');
            Route::patch('/{user}/toggle-lock', 'toggleLock');
            Route::delete('/{user}', 'destroy');
        });

        Route::prefix('categories')->controller(CategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('options', 'options');
            Route::get('/{category}', 'show');
            Route::post('/', 'store');
            Route::put('/{category}', 'update');
            Route::delete('/{category}', 'destroy');
        });

        Route::prefix('sub-categories')->controller(SubCategoryController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('options', 'options');
            Route::get('/{subCategory}', 'show');
            Route::post('/', 'store');
            Route::put('/{subCategory}', 'update');
            Route::delete('/{subCategory}', 'destroy');
        });
    });
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(function () {
    return response()->json(['message' => 'Not Found'], 404);
});

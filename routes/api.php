<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuperAdminApiController;
use App\Http\Controllers\Api\ApiTokenController;
use App\Http\Controllers\Api\GymApiController;

// Public API Login (Token creation)
Route::post('/login', [ApiTokenController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    
    // Authenticated User Info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Token Revocation Endpoints
    Route::delete('/tokens/current', [ApiTokenController::class, 'revokeCurrent']);
    Route::delete('/tokens/revoke-all', [ApiTokenController::class, 'revokeAll']);
    Route::delete('/tokens/{tokenId}', [ApiTokenController::class, 'revokeSpecific']);

    // Super Admin Sanctum API routes (role-protected + ability-checked)
    Route::middleware(['super_admin'])->prefix('admin')->group(function () {
        Route::get('/gyms', [SuperAdminApiController::class, 'gyms'])->middleware('abilities:manage:gyms');
        Route::get('/subscriptions', [SuperAdminApiController::class, 'subscriptions'])->middleware('abilities:manage:subscriptions');
        Route::get('/analytics', [SuperAdminApiController::class, 'analytics'])->middleware('abilities:read:analytics');
    });

    // Gym Admin API routes (ability-checked)
    Route::prefix('gym')->group(function () {
        Route::get('/bookings', [GymApiController::class, 'getGymBookings'])->middleware('abilities:manage:bookings');
        Route::get('/trainers', [GymApiController::class, 'getGymTrainers'])->middleware('abilities:manage:trainers');
    });

    // Trainer API routes (ability-checked)
    Route::prefix('trainer')->group(function () {
        Route::get('/workouts', [GymApiController::class, 'getTrainerWorkouts'])->middleware('abilities:manage:workouts');
    });

    // Member API routes (ability-checked)
    Route::prefix('member')->group(function () {
        Route::get('/bookings', [GymApiController::class, 'getMemberBookings'])->middleware('abilities:view:own-bookings');
        Route::post('/bookings', [GymApiController::class, 'createMemberBooking'])->middleware('abilities:create:bookings');
    });
});


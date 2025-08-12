<?php

use App\Http\Controllers\Api\AtendeeController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::middleware('throttle:5,3')->group(function () {
        Route::post('/auth/login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        // Route::get('/user', function (Request $request) {
        //     return $request->user();
        // });

        Route::post('/events', [EventController::class, 'store']);
        Route::put('/events/{id}', [EventController::class, 'update']);
        Route::delete('/events/{id}', [EventController::class, 'destroy']);

        Route::post('/events/{event}/atendees', [AtendeeController::class, 'store']);
        Route::delete('/events/{event}/atendees/{atendee}', [AtendeeController::class, 'destroy']);

        Route::post('/auth/logout', [AuthController::class, 'logout']);
    });

    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

    Route::get('/events/{event}/atendees', [AtendeeController::class, 'index']);
    Route::get('/events/{event}/atendees/{atendee}', [AtendeeController::class, 'show']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Not Found'
    ], 404);
});
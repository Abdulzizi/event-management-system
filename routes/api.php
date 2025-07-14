<?php

use App\Http\Controllers\Api\AtendeeController;
use App\Http\Controllers\Api\EventController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::apiResource('/events', EventController::class);

    Route::apiResource('/events.atendees', AtendeeController::class)
        ->scoped(['atendee' => 'event']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Not Found'
    ], 404);
});
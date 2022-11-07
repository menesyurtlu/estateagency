<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/', function () {
        return (object)[
            "info" => "Estate Agency API v1.0",
            "status" => true
        ];
    })->name('api');

    Route::get('unauthorized', function () {
        return \App\Http\Controllers\ApiResponses::send(401, "Unauthorized! Please provide a valid token.");
    })->name('unauthorized');

    Route::apiResources([
        'appointment' => 'AppointmentController',
    ]);

    Route::prefix('auth')->group(function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register']);
        Route::post('validate', [AuthController::class, 'validateSession']);
        Route::post('check', [AuthController::class, 'check']);
        Route::post('logout', [AuthController::class, 'logout']);
    });
});

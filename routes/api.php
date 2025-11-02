<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TrainingScheduleController;
use App\Http\Controllers\SolutionTrainingController;
use App\Http\Controllers\IndustryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::apiResource('partners', PartnerController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('training-schedules', TrainingScheduleController::class);
    Route::apiResource('solution-trainings', SolutionTrainingController::class);
    Route::apiResource('industries', IndustryController::class);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/enums/roles', [EnumController::class, 'roles']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

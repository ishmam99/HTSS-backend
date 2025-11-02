<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SoftwareSkillController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\UserSoftwareSkillController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/enums/roles', [EnumController::class, 'roles']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('software-skills', SoftwareSkillController::class);
        Route::apiResource('solutions', SolutionController::class);
        Route::apiResource('software', SoftwareController::class);
        Route::prefix('users/{userId}')->group(function () {
            Route::get('software-skills', [UserSoftwareSkillController::class, 'index']);
            Route::post('software-skills', [UserSoftwareSkillController::class, 'store']);
            Route::put('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'update']);
            Route::delete('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'destroy']);
        });
    });


});


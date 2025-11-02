<?php

use Illuminate\Support\Facades\Route;
use Modules\CRM\Controllers\ModuleController;
use Modules\CRM\Controllers\ModuleFieldController;
use Modules\CRM\Controllers\RecordController;
use Modules\CRM\src\Controllers\LeadController;

Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::prefix('crm')->group(function () {
       //add api routes for module
       Route::apiResource('module',ModuleController::class);
       Route::apiResource('field',ModuleFieldController::class);

        Route::prefix('modules/{module}')->group(function () {
            Route::get('/records', [RecordController::class, 'index']);
            Route::post('/records', [RecordController::class, 'store'])->middleware('auth:sanctum');
            Route::get('/fields', [ModuleFieldController::class, 'getByModule']);
        });
        Route::post('/convert-to-accounts/{recordId}', [RecordController::class, 'convertModule']);

    });
});

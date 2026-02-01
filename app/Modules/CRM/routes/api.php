<?php

use App\Http\Controllers\ModuleImportController;
use Illuminate\Support\Facades\Route;
use Modules\CRM\Controllers\ActivityController;
use Modules\CRM\Controllers\CustomViewController;
use Modules\CRM\Controllers\ModuleController;
use Modules\CRM\Controllers\ModuleFieldController;
use Modules\CRM\Controllers\RecordController;
use Modules\CRM\Controllers\RecordValueController;

Route::prefix('api/v1')->middleware('api')->group(function () {
    Route::prefix('crm')->middleware('auth:sanctum')->group(function () {
        // add api routes for module
        Route::get('activities', [ActivityController::class, 'index']);
        Route::get('activities/{id}', [ActivityController::class, 'show']);
        Route::get('modules', [ModuleController::class,'index']);
        Route::apiResource('field', ModuleFieldController::class);
        Route::get('stats', [ModuleController::class, 'stats']);
        Route::prefix('modules/{module}')->group(function () {
            Route::get('/records', action: [RecordController::class, 'index']);
            Route::get('/records/{id}', [RecordController::class, 'show']);
            Route::post('/records', [RecordController::class, 'store'])->middleware('auth:sanctum');
            Route::get('/fields', [ModuleFieldController::class, 'getByModule']);
            Route::post('/import/excel', [ModuleImportController::class, 'import']);
        });
        Route::post('/assign-record/{record}', [RecordController::class, 'assignRecord'])->middleware('auth:sanctum');
        Route::post('/assign-records', [RecordController::class, 'assignRoleToMultipleRecords'])->middleware('auth:sanctum');
        Route::post('/convert-to-accounts/{recordId}', [RecordController::class, 'convertModule']);
        Route::get('/record-values/{recordId}', [RecordController::class, 'getByRecord']);
        Route::post('/record-child-create', [RecordController::class, 'addChild']);
        Route::get('/record-child-get/{record}/{type}', [RecordController::class, 'getChild']);
        Route::post('/create-values/{id}', [RecordController::class, 'storeRecordValue']);
        Route::put('/record-values/{id}', [RecordController::class, 'updateValue']);
        Route::put('/assign-record-update/{id}', [RecordController::class, 'updateRecordAssignment']);
        Route::delete('/record/{record}', [RecordController::class, 'destroy']);
        Route::get('convert-deal-to-project/{dealId}', [RecordController::class, 'convertDealToProject']);
        Route::post('bulk-update-records', [RecordValueController::class, 'bulkUpdateOrCreate']);
        Route::post('custom-views', [CustomViewController::class, 'store']);
        Route::delete('custom-view-delete/{id}', [CustomViewController::class, 'destroy']);
        Route::get('my-custom-views', [CustomViewController::class, 'index']);
        Route::get('my-custom-views/{id}', [CustomViewController::class, 'show']);
    });
});

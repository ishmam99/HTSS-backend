<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SoftwareSkillController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\UserSoftwareSkillController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\TrainingSessionController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\SolutionTrainingController;
use App\Http\Controllers\IndustryController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');




Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);


    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/enums/roles', [EnumController::class, 'roles']);
        Route::post('/logout', [AuthController::class, 'logout']);
     Route::apiResource('partners', PartnerController::class);
    Route::apiResource('customers', CustomerController::class);
    Route::apiResource('training-schedules', TrainingScheduleController::class);
    Route::apiResource('solution-trainings', SolutionTrainingController::class);

    Route::post('add-industry-solutions',[SoftwareController::class, 'industrySolution']);
    Route::post('add-industry-softwares',[SoftwareController::class, 'industrySoftware']);
    Route::post('add-software-solutions',[SoftwareController::class, 'softwareSolution']);
      Route::apiResource('trainings', TrainingController::class);
    Route::apiResource('industries', IndustryController::class);
        Route::apiResource('software-skills', SoftwareSkillController::class);
        Route::apiResource('solutions', SolutionController::class);
        Route::apiResource('softwares', SoftwareController::class);
        Route::apiResource('training-sessions', TrainingSessionController::class);
        Route::apiResource('training-enrollments', TrainingEnrollmentController::class);
        Route::prefix('users/{userId}')->group(function () {
            Route::get('software-skills', [UserSoftwareSkillController::class, 'index']);
            Route::post('software-skills', [UserSoftwareSkillController::class, 'store']);
            Route::put('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'update']);
            Route::delete('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'destroy']);


        });
    });




});

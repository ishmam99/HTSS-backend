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
use App\Http\Controllers\CustomerSoftwareController;
use App\Http\Controllers\CustomerSolutionController;
use App\Http\Controllers\CustomerStatsController;
use App\Http\Controllers\CustomerSupportController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\EndUserSoftwareController;
use App\Http\Controllers\EndUserTrainingController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\IssueTicketController;
use App\Http\Controllers\OnsiteSupportTicketController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainingCourseController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\TrainingEventController;
use App\Http\Controllers\TrainingOfferController;

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
        // Route::apiResource('training-schedules', TrainingScheduleController::class);
        // Route::apiResource('solution-trainings', SolutionTrainingController::class);
        Route::get('users',[AuthController::class,'index']);
        Route::post('add-industry-solutions', [SoftwareController::class, 'industrySolution']);
        Route::post('add-industry-softwares', [SoftwareController::class, 'industrySoftware']);
        Route::post('add-software-solutions', [SoftwareController::class, 'softwareSolution']);
        // Route::apiResource('trainings', TrainingController::class);
        Route::apiResource('industries', IndustryController::class);
        Route::apiResource('software-skills', SoftwareSkillController::class);
        Route::apiResource('solutions', SolutionController::class);
        Route::apiResource('softwares', SoftwareController::class);
        Route::get('software-stats',[ SoftwareController::class,'stats']);
        // Route::apiResource('training-sessions', TrainingSessionController::class);
        // Route::apiResource('training-enrollments', TrainingEnrollmentController::class);
        Route::prefix('users/{userId}')->group(function () {
            Route::get('software-skills', [UserSoftwareSkillController::class, 'index']);
            Route::post('software-skills', [UserSoftwareSkillController::class, 'store']);
            Route::put('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'update']);
            Route::delete('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'destroy']);
        });
    Route::apiResource('issue-ticket',IssueTicketController::class);
    Route::apiResource('end-users', EndUserController::class);
    Route::apiResource('training-course', TrainingCourseController::class);
    Route::apiResource('trainer', TrainerController::class);
    Route::apiResource('training-event',TrainingEventController::class);

    Route::apiResource('training-offer',TrainingOfferController::class);
    Route::apiResource('training-enrollment',TrainingEnrollmentController::class);

    Route::apiResource('onsite-support-ticket',OnsiteSupportTicketController::class);
    Route::get('customer-software',[CustomerSoftwareController::class, 'index']);
    Route::post('customer-software',[CustomerSoftwareController::class, 'store']);
    Route::post('customer-solution',[CustomerSolutionController::class, 'store']);
    Route::get('customer-solution',[CustomerSolutionController::class, 'index']);

    Route::get('customers/{customer}/stats', [CustomerStatsController::class, 'show']);
    Route::post('end-user-software-add',[EndUserSoftwareController::class, 'addSoftware']);
    Route::get('end-user-software-list',[EndUserSoftwareController::class, 'getSoftwares']);
    Route::get('end-user-solution-list',[EndUserSoftwareController::class, 'getSolutions']);
    Route::post('end-user-solution-add',[EndUserSoftwareController::class, 'addSolution']);
    Route::apiResource('end-user-trainings',EndUserTrainingController::class)->middleware('auth:sanctum');


    });
    Route::apiResource('customer-support',CustomerSupportController::class);
    Route::put('customer-support-status-update/{customerSupport}',[CustomerSupportController::class,'statusUpdate']);
    Route::get('/users/role-count', [EnumController::class, 'roleWiseCount']);
    Route::get('/users/role-get', [EnumController::class, 'roleWiseList']);
});

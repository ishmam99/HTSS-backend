<?php

use App\Http\Controllers\AppliedJobController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerSoftwareController;
use App\Http\Controllers\CustomerSolutionController;
use App\Http\Controllers\CustomerStatsController;
use App\Http\Controllers\CustomerSupportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\EndUserSoftwareController;
use App\Http\Controllers\EndUserTrainingController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\IssueTicketController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\OnsiteSupportTicketController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ScheduledMessageController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SoftwareLevelController;
use App\Http\Controllers\SoftwareSkillController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerCourseController;
use App\Http\Controllers\TrainerRequestFormController;
use App\Http\Controllers\TrainerScheduleController;
use App\Http\Controllers\TrainingCourseController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\TrainingEventController;
use App\Http\Controllers\TrainingOfferController;
use App\Http\Controllers\UserSoftwareSkillController;
use App\Http\Controllers\CustomerSuccessManagerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('role-by-user-list', [AuthController::class, 'usersByRole']);
    Route::get('users-role-wise-count', [AuthController::class, 'roleWiseCount']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/enums/roles', [EnumController::class, 'roles']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('partners', PartnerController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::post('assign-customer/{customer}', [CustomerController::class, 'assignCustomer']);
        Route::apiResource('customer-success-managers', CustomerSuccessManagerController::class);


        // Route::apiResource('training-schedules', TrainingScheduleController::class);
        // Route::apiResource('solution-trainings', SolutionTrainingController::class);
        Route::get('users', [AuthController::class, 'index']);
        Route::post('add-industry-solutions', [SoftwareController::class, 'industrySolution']);
        Route::post('add-industry-softwares', [SoftwareController::class, 'industrySoftware']);
        Route::post('add-software-solutions', [SoftwareController::class, 'softwareSolution']);
        // Route::apiResource('trainings', TrainingController::class);
        Route::apiResource('industries', IndustryController::class);
        Route::apiResource('software-skills', SoftwareSkillController::class);
        Route::apiResource('solutions', SolutionController::class);
        Route::apiResource('softwares', SoftwareController::class);
        Route::get('software-stats', [SoftwareController::class, 'stats']);
        // Route::apiResource('training-sessions', TrainingSessionController::class);
        // Route::apiResource('training-enrollments', TrainingEnrollmentController::class);
        Route::prefix('users/{userId}')->group(function () {
            Route::get('software-skills', [UserSoftwareSkillController::class, 'index']);
            Route::post('software-skills', [UserSoftwareSkillController::class, 'store']);
            Route::put('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'update']);
            Route::delete('software-skills/{softwareSkillId}', [UserSoftwareSkillController::class, 'destroy']);
        });
        Route::apiResource('issue-ticket', IssueTicketController::class);
        Route::apiResource('end-users', EndUserController::class);
        Route::apiResource('training-course', TrainingCourseController::class);
        Route::apiResource('trainer', TrainerController::class);
        Route::apiResource('training-event', TrainingEventController::class);

        Route::apiResource('training-offer', TrainingOfferController::class);
        Route::apiResource('training-enrollment', TrainingEnrollmentController::class);

        Route::apiResource('onsite-support-ticket', OnsiteSupportTicketController::class);
        Route::get('customer-software', [CustomerSoftwareController::class, 'index']);
        Route::post('customer-software', [CustomerSoftwareController::class, 'store']);
        Route::post('customer-solution', [CustomerSolutionController::class, 'store']);
        Route::get('customer-solution', [CustomerSolutionController::class, 'index']);

        Route::get('customers/{customer}/stats', [CustomerStatsController::class, 'show']);
        Route::post('end-user-software-add', [EndUserSoftwareController::class, 'addSoftware']);
        Route::get('end-user-software-list', [EndUserSoftwareController::class, 'getSoftwares']);
        Route::get('end-user-solution-list', [EndUserSoftwareController::class, 'getSolutions']);
        Route::post('end-user-solution-add', [EndUserSoftwareController::class, 'addSolution']);
        Route::apiResource('end-user-trainings', EndUserTrainingController::class)->middleware('auth:sanctum');

        Route::apiResource('software-level', SoftwareLevelController::class)->middleware('auth:sanctum');
        Route::put('software-level-status-update/{id}', [SoftwareLevelController::class, 'update']);

        Route::apiResource('trainer-course', TrainerCourseController::class)->middleware('auth:sanctum');
        Route::put('trainer-course-status-update/{id}', [TrainerCourseController::class, 'statusUpdate']);

        Route::apiResource('trainer-schedule', TrainerScheduleController::class)->middleware('auth:sanctum');
        Route::put('trainer-schedule-status-update/{id}', [TrainerScheduleController::class, 'statusUpdate']);
        Route::apiResource('jobs-offer', JobController::class);
        Route::put('/publish-job/{id}', [JobController::class, 'publish']);
    });
    Route::apiResource('customer-support', CustomerSupportController::class);
    Route::put('customer-support-status-update/{customerSupport}', [CustomerSupportController::class, 'statusUpdate']);
    Route::get('industries', [IndustryController::class, 'index']);
    Route::get('solutions', [SolutionController::class, 'index']);
    Route::get('softwares', [SoftwareController::class, 'index']);
    Route::apiResource('customer-support', CustomerSupportController::class);
    Route::put('customer-support-status-update/{customerSupport}', [CustomerSupportController::class, 'statusUpdate']);
    Route::get('/users/role-count', [EnumController::class, 'roleWiseCount']);
    Route::get('/users/role-get', [EnumController::class, 'roleWiseList']);

    Route::apiResource('attendance', AttendanceController::class)->middleware('auth:sanctum');
    Route::put('status-update-attendance/{attendanceId}', [AttendanceController::class, 'attendanceStatusUpdate']);
    Route::put('status-update-attendance-time/{attendanceTimeId}', [AttendanceController::class, 'attendanceTimeStatusUpdate']);

    Route::apiResource('trainer-request-form', TrainerRequestFormController::class);
    Route::put('trainer-request-form-status-update/{id}', [TrainerRequestFormController::class, 'statusUpdate']);
    Route::put('job/{id}/status', [JobController::class, 'changeStatus']);
    Route::apiResource('department', DepartmentController::class);
    Route::apiResource('applied-jobs', AppliedJobController::class);
    Route::get('job-public', [JobController::class, 'publicJob']);
    Route::get('job-public/{id}', [JobController::class, 'publicJobShow']);
    Route::apiResource('positions', PositionController::class);
    Route::get('active-department', [DepartmentController::class, 'active']);
    Route::put('applied-job-status/{id}', [AppliedJobController::class, 'statusChange']);
    Route::apiResource('scheduled-messages', ScheduledMessageController::class);
    Route::put('scheduled-messages-status/{id}', [ScheduledMessageController::class, 'statusChange']);

    Route::get('/customers/by-user/{id}',[CustomerController::class, 'getByUser']);

    Route::get('/customer-success-managers/by-user/{userId}', [CustomerSuccessManagerController::class, 'getByUser']);

    Route::post('/attendance/login', [AttendanceController::class, 'login']);
    Route::post('/attendance/logout/{id}', [AttendanceController::class, 'logout']);

});

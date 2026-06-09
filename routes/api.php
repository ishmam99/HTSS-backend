<?php

use App\Http\Controllers\Api\CompetencyController;
use App\Http\Controllers\Api\GeneralSkillController;
use App\Http\Controllers\Api\StripeWebhookController;
use App\Http\Controllers\Api\TrainingScheduleController;
use App\Http\Controllers\AppliedJobController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyUserCustomerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerSoftwareController;
use App\Http\Controllers\CustomerSolutionController;
use App\Http\Controllers\CustomerStatsController;
use App\Http\Controllers\CustomerSuccessManagerController;
use App\Http\Controllers\CustomerSupportController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EndUserController;
use App\Http\Controllers\EndUserRoadMapController;
use App\Http\Controllers\EndUserSoftwareController;
use App\Http\Controllers\EndUserTrainingController;
use App\Http\Controllers\EnumController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InternalTrainingController;
use App\Http\Controllers\IssueTicketController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\MeetingScheduleController;
use App\Http\Controllers\MonthlyCSMActivityController;
use App\Http\Controllers\OnsiteSupportTicketController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\ProfessionalReferenceController;
use App\Http\Controllers\ProfessionSummaryController;
use App\Http\Controllers\ScheduledMessageController;
use App\Http\Controllers\SoftwareController;
use App\Http\Controllers\SoftwareLevelController;
use App\Http\Controllers\SoftwareRequestController;
use App\Http\Controllers\SoftwareSkillController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\SuccessTeamActivityReportController;
use App\Http\Controllers\SuccessTeamController;
use App\Http\Controllers\SuccessTeamRoleController;
use App\Http\Controllers\SuccessTeamTaskController;
use App\Http\Controllers\TrainerController;
use App\Http\Controllers\TrainerCourseController;
use App\Http\Controllers\TrainerPreferdScheduleController;
use App\Http\Controllers\TrainerRequestFormController;
use App\Http\Controllers\TrainerScheduleController;
use App\Http\Controllers\TrainerSkillController;
use App\Http\Controllers\TrainingCourseController;
use App\Http\Controllers\TrainingCourseScheduleController;
use App\Http\Controllers\TrainingEnrollmentController;
use App\Http\Controllers\TrainingEventController;
use App\Http\Controllers\TrainingOfferController;
use App\Http\Controllers\UserEducationController;
use App\Http\Controllers\UserExperienceController;
use App\Http\Controllers\UserResumeController;
use App\Http\Controllers\UserSoftwareSkillController;
use App\Http\Controllers\TrainingRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);
    Route::post('/stripe/create-payment-intent', [StripeWebhookController::class, 'createPaymentIntent']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('role-by-user-list', [AuthController::class, 'usersByRole']);
    Route::get('users-role-wise-count', [AuthController::class, 'roleWiseCount']);
    Route::apiResource('internal-trainings', InternalTrainingController::class);
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('employees', EmployeeController::class);
        Route::apiResource('success-team-roles', SuccessTeamRoleController::class);
        Route::post('/set-user-role', [AuthController::class, 'setRole']);
        Route::post('/set-user-role', [AuthController::class, 'setRole']);
        Route::get('/enums/roles', [EnumController::class, 'roles']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::apiResource('partners', PartnerController::class);
        Route::apiResource('customers', CustomerController::class);
        Route::post('assign-customer/{customer}', [CustomerController::class, 'assignCustomer']);
        Route::apiResource('customer-success-managers', CustomerSuccessManagerController::class);
        Route::get('my-success-team', [SuccessTeamController::class, 'mySuccessTeams']);

        Route::apiResource('training-schedules', TrainingScheduleController::class);

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

        Route::get('end-user-by-user-id/{id}', [EndUserController::class, 'getUserByUserId']);
        Route::get('end-user-by-user-id/{id}', [EndUserController::class, 'getUserByUserId']);
        Route::apiResource('training-course', TrainingCourseController::class);
        Route::get(
            '/training-courses/by-company/{company}',
            [TrainingCourseController::class, 'getByCompany']
        );
        Route::get('/training-courses/by-company/{company}',
            [TrainingCourseController::class, 'getByCompany']
        );
        Route::apiResource('trainer', TrainerController::class);
        Route::apiResource('training-event', TrainingEventController::class);

        Route::apiResource('training-offer', TrainingOfferController::class);
        Route::apiResource('training-enrollment', TrainingEnrollmentController::class);

        Route::apiResource('onsite-support-ticket', OnsiteSupportTicketController::class);
        Route::get('customer-software', [CustomerSoftwareController::class, 'index']);
        Route::post('customer-software', [CustomerSoftwareController::class, 'store']);
        Route::put('customer-software/{id}', [CustomerSoftwareController::class, 'update']);
        Route::post('customer-solution', [CustomerSolutionController::class, 'store']);
        Route::put('customer-solution/{id}', [CustomerSolutionController::class, 'update']);
        Route::get('customer-solution', [CustomerSolutionController::class, 'index']);

        Route::get('customers/{customer}/stats', [CustomerStatsController::class, 'show']);
        Route::post('end-user-software-add', [EndUserSoftwareController::class, 'addSoftware']);
        Route::get('end-user-software-list', [EndUserSoftwareController::class, 'getSoftwares']);
        Route::get('end-user-solution-list', [EndUserSoftwareController::class, 'getSolutions']);
        Route::post('end-user-solution-add', [EndUserSoftwareController::class, 'addSolution']);
        Route::post('end-users/import', [EndUserController::class, 'import']);
        Route::post('end-users/import', [EndUserController::class, 'import']);
        Route::apiResource('end-user-trainings', EndUserTrainingController::class)->middleware('auth:sanctum');
        Route::apiResource('end-user-road-maps', EndUserRoadMapController::class)->middleware('auth:sanctum');

        Route::apiResource('software-level', SoftwareLevelController::class)->middleware('auth:sanctum');
        Route::put('software-level-status-update/{id}', [SoftwareLevelController::class, 'update']);

        Route::apiResource('trainer-course', TrainerCourseController::class)->middleware('auth:sanctum');
        Route::put('trainer-course-status-update/{id}', [TrainerCourseController::class, 'statusUpdate']);

        Route::apiResource('trainer-schedule', TrainerScheduleController::class)->middleware('auth:sanctum');
        Route::put('trainer-schedule-status-update/{id}', [TrainerScheduleController::class, 'statusUpdate']);
        Route::apiResource('jobs-offer', JobController::class);
        Route::put('/publish-job/{id}', [JobController::class, 'publish']);

        Route::post('/attendance/login', [AttendanceController::class, 'login']);
        Route::post('/attendance/logout/{id}', [AttendanceController::class, 'logout']);
        Route::post('/attendance-time', [AttendanceController::class, 'attendanceTimeStore']);
        Route::apiResource('monthly-csm-activity', MonthlyCSMActivityController::class);
        Route::get('monthly-activity', [MonthlyCSMActivityController::class, 'activityByUser']);
        Route::apiResource('success-team-tasks', SuccessTeamTaskController::class);
        Route::post('success-team-tasks/{task}/outputs', [SuccessTeamTaskController::class, 'storeOutput']);
        Route::put('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'updateOutput']);
        Route::delete('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'deleteOutput']);
        Route::get('my-success-team-task-outputs', [SuccessTeamTaskController::class, 'myOutputs']);
        Route::get('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'teamOutputs']);
        Route::post('success-teams/{team}/assign', [SuccessTeamController::class, 'assign']);
        Route::apiResource('success-team-activity-reports', SuccessTeamActivityReportController::class);
        Route::get('monthly-activity', [MonthlyCSMActivityController::class, 'activityByUser']);
        Route::apiResource('success-team-tasks', SuccessTeamTaskController::class);
        Route::post('success-team-tasks/{task}/outputs', [SuccessTeamTaskController::class, 'storeOutput']);
        Route::put('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'updateOutput']);
        Route::delete('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'deleteOutput']);
        Route::get('my-success-team-task-outputs', [SuccessTeamTaskController::class, 'myOutputs']);
        Route::get('success-team-task-outputs/{id}', [SuccessTeamTaskController::class, 'teamOutputs']);
        Route::post('success-teams/{team}/assign', [SuccessTeamController::class, 'assign']);
        Route::apiResource('success-team-activity-reports', SuccessTeamActivityReportController::class);
        Route::resource('general-skills', GeneralSkillController::class);
        Route::get('/general-skill-by-user', [GeneralSkillController::class, 'getGeneralSkillByUser']);
        Route::get('/task-outputs-by-team-and-date-range/{team_id}', [SuccessTeamActivityReportController::class, 'getTaskOutputsByTeamAndDateRange']);
        Route::resource('competencies', CompetencyController::class);
        Route::apiResource('applied-jobs', AppliedJobController::class);
        Route::post('/applied-jobs/{id}/generate-link', [AppliedJobController::class, 'generateAccessLink']);
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

    // In routes/api.php

// Status update (generic)
// Route::patch('/trainer-request-forms/{id}/status', [TrainerRequestFormController::class, 'statusUpdate']);

// Or use specific methods for better clarity
Route::post('/trainer-request-form/{id}/approve', [TrainerRequestFormController::class, 'approve']);
Route::post('/trainer-request-form/{id}/reject', [TrainerRequestFormController::class, 'reject']);
    Route::put('job/{id}/status', [JobController::class, 'changeStatus']);
    Route::apiResource('department', DepartmentController::class);
    Route::post('applied-jobs', [AppliedJobController::class, 'store']);
    Route::apiResource('end-users', EndUserController::class);
    // HR generates link

    // Applicant access
    Route::get('/applicant-access/{token}', [AppliedJobController::class, 'accessByToken']);
    Route::post('/applicant-access/{token}', [AppliedJobController::class, 'updateByToken']);
    Route::get('job-public', [JobController::class, 'publicJob']);
    Route::get('job-public/{job_offer}', [JobController::class, 'publicJobShow']);
    Route::apiResource('positions', PositionController::class);
    Route::get('active-department', [DepartmentController::class, 'active']);
    Route::put('applied-job-status/{id}', [AppliedJobController::class, 'statusChange']);
    Route::apiResource('scheduled-messages', ScheduledMessageController::class);
    Route::put('scheduled-messages-status/{id}', [ScheduledMessageController::class, 'statusChange']);

    Route::get('/customers/by-user/{id}', [CustomerController::class, 'getByUser']);

    Route::get('/customer-success-managers/by-user/{userId}', [CustomerSuccessManagerController::class, 'getByUser']);
    Route::apiResource('companies', CompanyController::class);
    Route::apiResource('company-user-customer', CompanyUserCustomerController::class);
    Route::apiResource('success-teams', SuccessTeamController::class);
    // Assign members & companies separately

    Route::post('assign-customers/{companyId}', [CompanyController::class, 'assignCustomers']);
    Route::get('success-team/{success_team_id}/customers', [SuccessTeamController::class, 'getCustomersBySuccessTeam']);
    Route::get('companies/{company_id}/csm-reports', [MonthlyCSMActivityController::class, 'getCompanyCSMReports']);
    Route::get('/success-teams/{success_team_id}/companies/customers', [SuccessTeamController::class, 'getSuccessTeamCompaniesCustomers']);
    Route::get('/success-teams/{success_team_id}/companies', [SuccessTeamController::class, 'getSuccessTeamCompanies']);
    Route::apiResource('user-education', UserEducationController::class)->middleware('auth:sanctum');
    Route::apiResource('user-experiences', UserExperienceController::class)->middleware('auth:sanctum');
    Route::apiResource('professional-references', ProfessionalReferenceController::class)->middleware('auth:sanctum');
    Route::apiResource('professional-summary', ProfessionSummaryController::class)->middleware('auth:sanctum');
    Route::post('/send-email', [EndUserController::class, 'emailSend']);
    Route::apiResource('user-resumes', UserResumeController::class)->middleware('auth:sanctum');
    Route::prefix('training-requests')->group(function () {
    // Public routes (no auth required for submission)


    // Admin routes (add auth middleware in production)
    Route::middleware(['auth:sanctum'])->group(function () {
            Route::post('/', [TrainingRequestController::class, 'store']);
        Route::get('/', [TrainingRequestController::class, 'index']);

        Route::get('/dashboard', [TrainingRequestController::class, 'dashboard']);
          Route::get('/stats', [TrainingRequestController::class, 'stats']);
        Route::get('/reports', [TrainingRequestController::class, 'reports']);
        Route::get('/software-list', [TrainingRequestController::class, 'getSoftwareList']);
        Route::get('/export', [TrainingRequestController::class, 'export']);
        Route::get('/{id}', [TrainingRequestController::class, 'show']);
        Route::put('/{id}', [TrainingRequestController::class, 'update']);
        Route::delete('/{id}', [TrainingRequestController::class, 'destroy']);
        Route::post('/bulk-delete', [TrainingRequestController::class, 'bulkDelete']);
        Route::post('/{id}/status', [TrainingRequestController::class, 'updateStatus']);
        Route::post('/{id}/schedule', [TrainingRequestController::class, 'schedule']);
        Route::post('/{id}/complete', [TrainingRequestController::class, 'complete']);
        Route::post('/{id}/payment', [TrainingRequestController::class, 'recordPayment']);
        Route::post('/{id}/certificate', [TrainingRequestController::class, 'issueCertificate']);
    });
});
    Route::apiResource('meeting-schedules', MeetingScheduleController::class)->middleware('auth:sanctum');
    Route::prefix('trainer-preferred-schedules')->group(function () {
    Route::get('/', [TrainerPreferdScheduleController::class, 'index']);
    Route::post('/', [TrainerPreferdScheduleController::class, 'store']);
    Route::get('/{id}', [TrainerPreferdScheduleController::class, 'show']);
    Route::put('/{id}', [TrainerPreferdScheduleController::class, 'update']);
    Route::delete('/{id}', [TrainerPreferdScheduleController::class, 'destroy']);
});

// Trainer Skills Routes
Route::prefix('trainer-skills')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [TrainerSkillController::class, 'index']);
    Route::post('/', [TrainerSkillController::class, 'store']);
    Route::get('/{id}', [TrainerSkillController::class, 'show']);
    Route::put('/{id}', [TrainerSkillController::class, 'update']);
    Route::delete('/{id}', [TrainerSkillController::class, 'destroy']);
});


// Admin routes (protected with auth and admin middleware)
Route::middleware(['auth:sanctum'])->prefix('admin')->group(function () {
    // Training Course Schedules
    Route::apiResource('training-course-schedules', TrainingCourseScheduleController::class);
    Route::post('training-course-schedules/bulk-delete', [TrainingCourseScheduleController::class, 'bulkDestroy']);
    Route::patch('training-course-schedules/{id}/status', [TrainingCourseScheduleController::class, 'updateStatus']);
    Route::get('training-course-schedules/trainer/{trainerId}', [TrainingCourseScheduleController::class, 'schedulesByTrainer']);
    Route::get('training-course-schedules/calendar/events', [TrainingCourseScheduleController::class, 'calendar']);
      Route::get('training-course-schedules/monthly/grouped', [TrainingCourseScheduleController::class, 'getSchedulesGroupedByMonth']);
    Route::get('training-course-schedules/monthly/calendar', [TrainingCourseScheduleController::class, 'getMonthlyCalendar']);
    Route::get('training-course-schedules/monthly/statistics', [TrainingCourseScheduleController::class, 'getMonthlyStatistics']);
    Route::get('training-course-schedules/available-months', [TrainingCourseScheduleController::class, 'getAvailableMonths']);
});

// Public routes (no authentication required or with optional auth)
Route::prefix('public')->group(function () {
    Route::get('training-course-schedules', [TrainingCourseScheduleController::class, 'publicIndex']);
    Route::get('training-course-schedules/{id}', [TrainingCourseScheduleController::class, 'publicShow']);
    Route::get('training-course-schedules/{id}/availability', [TrainingCourseScheduleController::class, 'checkAvailability']);
    Route::get('courses/{courseId}/upcoming-schedules', [TrainingCourseScheduleController::class, 'upcomingSchedules']);
     Route::get('training-course-schedules/monthly/available-courses', [TrainingCourseScheduleController::class, 'getAvailableCoursesByMonth']);
    Route::get('training-course-schedules/monthly/calendar', [TrainingCourseScheduleController::class, 'getMonthlyCalendar']);
});
Route::apiResource('software-requests', SoftwareRequestController::class);

// Additional custom routes
Route::prefix('software-requests')->group(function () {
    Route::put('{id}/status', [SoftwareRequestController::class, 'updateStatus']);
    Route::put('{id}/conversion-status', [SoftwareRequestController::class, 'updateConversionStatus']);
});

});

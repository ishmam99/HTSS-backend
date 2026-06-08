<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use App\Models\TrainingCourseSchedule;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TrainingCourseScheduleController extends Controller
{
    /**
     * Display a listing of schedules (Admin)
     */
      public function index(Request $request)
    {
        $query = TrainingCourseSchedule::advancedQuery($request);
        $lists = $request->per_page
            ? $query->paginate($request->per_page)
            : $query->get();

        return response()->json([
                'success' => true,
                'data' => $lists,
                'total' => TrainingCourseSchedule::count()
            ]);
    }
    
    /**
     * Display public schedules (only upcoming and active)
     */
    public function publicIndex(Request $request)
    {
        $query = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])
            ->where('date', '>=', now()->toDateString())
            ->where('status', 1);

        // Filter by course
        if ($request->has('training_course_id')) {
            $query->where('training_course_id', $request->training_course_id);
        }

        // Filter by trainer
        if ($request->has('trainer_id')) {
            $query->where('trainer_id', $request->trainer_id);
        }

        // Sort by date
        $query->orderBy('date', 'asc');

        $perPage = $request->get('per_page', 15);
        $schedules = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $schedules,
            'message' => 'Public schedules retrieved successfully'
        ]);
    }

    /**
     * Store a newly created schedule (Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'training_course_id' => 'required|exists:training_courses,id',
            'date' => 'required|date|after_or_equal:today',
            'trainer_id' => 'nullable|exists:users,id',
            'available_seats' => 'required|integer|min:1',
            'status' => 'nullable|in:0,1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Check if schedule already exists for this course on same date
        $existingSchedule = TrainingCourseSchedule::where('training_course_id', $request->training_course_id)
            ->where('date', $request->date)
            ->first();

        if ($existingSchedule) {
            return response()->json([
                'success' => false,
                'message' => 'A schedule already exists for this course on the selected date'
            ], 409);
        }

        $schedule = TrainingCourseSchedule::create([
            'training_course_id' => $request->training_course_id,
            'date' => $request->date,
            'trainer_id' => $request->trainer_id,
            'available_seats' => $request->available_seats,
            'status' => $request->status ?? 0
        ]);

        $schedule->load(['trainingCourse', 'trainer']);

        return response()->json([
            'success' => true,
            'data' => $schedule,
            'message' => 'Schedule created successfully'
        ], 201);
    }

    /**
     * Display the specified schedule (Admin)
     */
    public function show($id)
    {
        $schedule = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
            'message' => 'Schedule retrieved successfully'
        ]);
    }

    /**
     * Display public schedule details
     */
    public function publicShow($id)
    {
        $schedule = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])
            ->where('status', 1)
            ->where('date', '>=', now()->toDateString())
            ->find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found or not available'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $schedule,
            'message' => 'Schedule retrieved successfully'
        ]);
    }

    /**
     * Update the specified schedule (Admin)
     */
    public function update(Request $request, $id)
    {
        $schedule = TrainingCourseSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'training_course_id' => 'sometimes|exists:training_courses,id',
            'date' => 'sometimes|date|after_or_equal:today',
            'trainer_id' => 'nullable|exists:users,id',
            'available_seats' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:0,1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // Check for duplicate schedule if date or course is being changed
        if (($request->has('training_course_id') || $request->has('date')) &&
            $request->training_course_id != $schedule->training_course_id ||
            $request->date != $schedule->date
        ) {

            $courseId = $request->training_course_id ?? $schedule->training_course_id;
            $date = $request->date ?? $schedule->date;

            $existingSchedule = TrainingCourseSchedule::where('training_course_id', $courseId)
                ->where('date', $date)
                ->where('id', '!=', $id)
                ->first();

            if ($existingSchedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'A schedule already exists for this course on the selected date'
                ], 409);
            }
        }

        $schedule->update($request->only([
            'training_course_id',
            'date',
            'trainer_id',
            'available_seats',
            'status'
        ]));

        $schedule->load(['trainingCourse', 'trainer']);

        return response()->json([
            'success' => true,
            'data' => $schedule,
            'message' => 'Schedule updated successfully'
        ]);
    }

    /**
     * Remove the specified schedule (Admin)
     */
    public function destroy($id)
    {
        $schedule = TrainingCourseSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }

        // Check if schedule has any enrollments before deletion
        // if ($schedule->enrollments()->count() > 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Cannot delete schedule with existing enrollments'
        //     ], 409);
        // }

        $schedule->delete();

        return response()->json([
            'success' => true,
            'message' => 'Schedule deleted successfully'
        ]);
    }

    /**
     * Bulk delete schedules (Admin)
     */
    public function bulkDestroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:training_course_schedules,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        // // Check for schedules with enrollments
        // $schedulesWithEnrollments = TrainingCourseSchedule::whereIn('id', $request->ids)
        //     ->has('enrollments')
        //     ->pluck('id');

        // if ($schedulesWithEnrollments->count() > 0) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Some schedules have enrollments and cannot be deleted',
        //         'data' => ['schedules_with_enrollments' => $schedulesWithEnrollments]
        //     ], 409);
        // }

        $deletedCount = TrainingCourseSchedule::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} schedules deleted successfully"
        ]);
    }

    /**
     * Update schedule status (Admin)
     */
    public function updateStatus(Request $request, $id)
    {
        $schedule = TrainingCourseSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1,2'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
                'message' => 'Validation failed'
            ], 422);
        }

        $schedule->status = $request->status;
        $schedule->save();

        return response()->json([
            'success' => true,
            'data' => $schedule,
            'message' => 'Schedule status updated successfully'
        ]);
    }

    /**
     * Check seat availability
     */
    public function checkAvailability($id)
    {
        $schedule = TrainingCourseSchedule::find($id);

        if (!$schedule) {
            return response()->json([
                'success' => false,
                'message' => 'Schedule not found'
            ], 404);
        }

        // $bookedSeats = $schedule->enrollments()->count();
        $availableSeats = $schedule->available_seats;
        // $availableSeats = $schedule->available_seats - $bookedSeats;

        return response()->json([
            'success' => true,
            'data' => [
                'total_seats' => $schedule->available_seats,
                // 'booked_seats' => $bookedSeats,
                'available_seats' => max(0, $availableSeats),
                'is_available' => $availableSeats > 0 && $schedule->status == 1
            ],
            'message' => 'Availability checked successfully'
        ]);
    }

    /**
     * Get upcoming schedules for a specific course
     */
    public function upcomingSchedules($courseId)
    {
        $course = TrainingCourse::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found'
            ], 404);
        }

        $schedules = TrainingCourseSchedule::with('trainer')
            ->where('training_course_id', $courseId)
            ->where('date', '>=', now()->toDateString())
            ->where('status', 2)
            ->orderBy('date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $schedules,
            'message' => 'Upcoming schedules retrieved successfully'
        ]);
    }

    /**
     * Get schedules by trainer (Admin)
     */
    public function schedulesByTrainer($trainerId)
    {
        $trainer = User::find($trainerId);

        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Trainer not found'
            ], 404);
        }

        $schedules = TrainingCourseSchedule::with(['trainingCourse'])
            ->where('trainer_id', $trainerId)
            ->orderBy('date', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $schedules,
            'message' => 'Trainer schedules retrieved successfully'
        ]);
    }

    /**
     * Get calendar view of schedules (Admin)
     */
    public function calendar(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $schedules = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->groupBy('date');

        $calendarData = [];
        foreach ($schedules as $date => $dateSchedules) {
            $calendarData[$date] = $dateSchedules->map(function ($schedule) {
                return [
                    'id' => $schedule->id,
                    'course_name' => $schedule->trainingCourse->name,
                    'trainer_name' => $schedule->trainer ? $schedule->trainer->name : 'Not Assigned',
                    'available_seats' => $schedule->available_seats,
                    'status' => $schedule->status,
                    'status_label' => $schedule->status == 1 ? 'Active' : ($schedule->status == 0 ? 'Draft' : 'Cancelled')
                ];
            });
        }

        return response()->json([
            'success' => true,
            'data' => $calendarData,
            'message' => 'Calendar data retrieved successfully'
        ]);
    }

    /**
     * Get schedules grouped by month (Admin)
     */
    public function getSchedulesGroupedByMonth(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
            'training_course_id' => 'nullable|exists:training_courses,id',
            'status' => 'nullable|in:0,1,2'
        ]);

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        $query = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])
            ->whereYear('date', $year);

        if ($month) {
            $query->whereMonth('date', $month);
        }

        if ($request->has('training_course_id')) {
            $query->where('training_course_id', $request->training_course_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $schedules = $query->orderBy('date', 'asc')->get();

        // Group by month
        $groupedSchedules = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->date)->format('F Y'); // e.g., "January 2024"
        });

        $result = [];
        foreach ($groupedSchedules as $monthYear => $monthSchedules) {
            $result[] = [
                'month_year' => $monthYear,
                'month' => $monthSchedules->first()->date->month,
                'year' => $monthSchedules->first()->date->year,
                'total_schedules' => $monthSchedules->count(),
                'total_available_seats' => $monthSchedules->sum('available_seats'),
                'schedules' => $monthSchedules
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'month' => $month,
                'groups' => $result
            ],
            'message' => 'Schedules grouped by month retrieved successfully'
        ]);
    }

    /**
     * Get available courses grouped by month for public view
     */
    public function getAvailableCoursesByMonth(Request $request)
    {
        $year = $request->get('year', now()->year);

        // Get active schedules with available seats for all months
        $schedules = TrainingCourseSchedule::with(['trainingCourse.solution','trainingCourse.software', 'trainer'])
            ->where('status', 2) // Active status only
            ->where('date', '>=', now()->toDateString())
            ->whereYear('date', $year)
            ->orderBy('date', 'asc')
            ->get();

        // Group by month and course
        $coursesByMonth = $schedules->groupBy(function ($schedule) {
            return Carbon::parse($schedule->date)->format('Y-m');
        })->map(function ($monthSchedules, $monthYear) {
            return [
                'month_year' => $monthYear,
                'month' => Carbon::parse($monthSchedules->first()->date)->month,
                'month_name' => Carbon::parse($monthSchedules->first()->date)->format('F'),
                'year' => Carbon::parse($monthSchedules->first()->date)->year,
                'courses' => $monthSchedules->groupBy('training_course_id')->map(function ($courseSchedules) {
                    $course = $courseSchedules->first()->trainingCourse;
                    return [
                        'course_id' => $course->id,
                        'course_name' => $course->name,
                        'course_details' => $course,
                        'course_description' => $course->description,
                        'total_schedules' => $courseSchedules->count(),
                        'total_available_seats' => $courseSchedules->sum(function ($schedule) {
                            return $schedule->available_seats_count;
                        }),
                        'schedules' => $courseSchedules->map(function ($schedule) {
                            return [
                                'schedule_id' => $schedule->id,
                                'date' => $schedule->date,
                                'date_formatted' => Carbon::parse($schedule->date)->format('l, F j, Y'),
                                'available_seats' => $schedule->available_seats_count,
                                'trainer_name' => $schedule->trainer ? $schedule->trainer->name : 'TBD'
                            ];
                        })
                    ];
                })->values()
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'groups' => $coursesByMonth
            ],
            'message' => 'Available courses by month retrieved successfully'
        ]);
    }

    /**
     * Get monthly schedule calendar with course details
     */
    public function getMonthlyCalendar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2000|max:2100',
            'month' => 'nullable|integer|min:1|max:12',
            'training_course_id' => 'nullable|exists:training_courses,id'
        ]);

        $year = $request->get('year', now()->year);
        $month = $request->get('month', now()->month);

        // Get all schedules for the month
        $query = TrainingCourseSchedule::with(['trainingCourse', 'trainer'])
            ->whereYear('date', $year)
            ->whereMonth('date', $month);

        if ($request->has('training_course_id')) {
            $query->where('training_course_id', $request->training_course_id);
        }

        $schedules = $query->orderBy('date')->get();

        // Create calendar array for the month
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $calendar = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = date("Y-m-d", strtotime("{$year}-{$month}-{$day}"));
            $daySchedules = $schedules->where('date', $date);

            $calendar[$day] = [
                'date' => $date,
                'day' => $day,
                'day_of_week' => date('l', strtotime($date)),
                'is_past' => $date < now()->toDateString(),
                'has_schedules' => $daySchedules->count() > 0,
                'schedules' => $daySchedules->map(function ($schedule) {
                    // $bookedSeats = $schedule->enrollments()->count();
                    return [
                        'schedule_id' => $schedule->id,
                        'course_id' => $schedule->training_course_id,
                        'course_name' => $schedule->trainingCourse->name,
                        'trainer_name' => $schedule->trainer ? $schedule->trainer->name : 'Not Assigned',
                        'total_seats' => $schedule->available_seats,
                        // 'booked_seats' => $bookedSeats,
                        // 'available_seats' => $schedule->available_seats - $bookedSeats,
                        'status' => $schedule->status,
                        'status_label' => $this->getStatusLabel($schedule->status)
                    ];
                })
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'calendar' => $calendar,
                'summary' => [
                    'total_schedules' => $schedules->count(),
                    'total_courses' => $schedules->unique('training_course_id')->count(),
                    'total_available_seats' => $schedules->sum('available_seats')
                ]
            ],
            'message' => 'Monthly calendar retrieved successfully'
        ]);
    }

    /**
     * Get monthly statistics (Admin dashboard)
     */
    public function getMonthlyStatistics(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'nullable|integer|min:2000|max:2100'
        ]);

        $year = $request->get('year', now()->year);

        $monthlyStats = [];
        for ($month = 1; $month <= 12; $month++) {
            $schedules = TrainingCourseSchedule::whereYear('date', $year)
                ->whereMonth('date', $month)
                ->get();

            $activeSchedules = $schedules->where('status', 1);
            $upcomingSchedules = $schedules->filter(function ($schedule) {
                return $schedule->date >= now()->toDateString();
            });

            $monthlyStats[] = [
                'month' => $month,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'total_schedules' => $schedules->count(),
                'active_schedules' => $activeSchedules->count(),
                'cancelled_schedules' => $schedules->where('status', 2)->count(),
                'draft_schedules' => $schedules->where('status', 0)->count(),
                'upcoming_schedules' => $upcomingSchedules->count(),
                'total_courses' => $schedules->unique('training_course_id')->count(),
                'total_available_seats' => $schedules->sum('available_seats'),
                'total_trainers' => $schedules->unique('trainer_id')->filter()->count()
            ];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'year' => $year,
                'statistics' => $monthlyStats
            ],
            'message' => 'Monthly statistics retrieved successfully'
        ]);
    }

    /**
     * Get month picker data with available months
     */
    public function getAvailableMonths()
    {
        // Get all months that have schedules
        $monthsWithSchedules = TrainingCourseSchedule::select(
            DB::raw('YEAR(date) as year'),
            DB::raw('MONTH(date) as month'),
            DB::raw('COUNT(*) as total_schedules')
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        $availableMonths = [];
        foreach ($monthsWithSchedules as $data) {
            $availableMonths[] = [
                'year' => $data->year,
                'month' => $data->month,
                'month_name' => date('F', mktime(0, 0, 0, $data->month, 1)),
                'total_schedules' => $data->total_schedules
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $availableMonths,
            'message' => 'Available months retrieved successfully'
        ]);
    }

    private function getStatusLabel($status)
    {
        switch ($status) {
            case 0:
                return 'Draft';
            case 1:
                return 'Active';
            case 2:
                return 'Cancelled';
            default:
                return 'Unknown';
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourseSchedule;
use App\Models\TrainingEnrollment;
use App\Models\TrainingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TrainingRequestController extends Controller
{
    /**
     * Display a listing of training requests.
     */
    public function index(Request $request)
    {
        $query = TrainingRequest::with(['user', 'trainingCourseSchedule.trainer', 'trainingEnrollment', 'trainingCourse']);

        // Restrict end-users to only their own requests
        if (auth()->user()->role == 'end-user') {
            $query->where('user_id', auth()->id());
        }

        // Filter trainingEnrollment - exclude records with no data
        if($request->has('enrolled') && $request->enrolled == 'yes') {
             $query->whereHas('trainingEnrollment');
        }

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by training type
        if ($request->has('training_type') && $request->training_type !== 'all') {
            $query->where('training_type', $request->training_type);
        }

        // Filter by software
        if ($request->has('software') && $request->software !== 'all') {
            $query->where('software', $request->software);
        }

        // Filter by date range
        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by name, email, organization, or course code
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('course_code', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Pagination
        $perPage = $request->get('per_page', 15);
        $trainingRequests = $query->paginate($perPage);

        // Get statistics for dashboard
        $statistics = [
            'total' => TrainingRequest::count(),
            'pending' => TrainingRequest::where('status', TrainingRequest::STATUS_PENDING)->count(),
            'under_review' => TrainingRequest::where('status', TrainingRequest::STATUS_UNDER_REVIEW)->count(),
            'approved' => TrainingRequest::where('status', TrainingRequest::STATUS_APPROVED)->count(),
            'scheduled' => TrainingRequest::where('status', TrainingRequest::STATUS_SCHEDULED)->count(),
            'completed' => TrainingRequest::where('status', TrainingRequest::STATUS_COMPLETED)->count(),
            'cancelled' => TrainingRequest::where('status', TrainingRequest::STATUS_CANCELLED)->count(),
            'rejected' => TrainingRequest::where('status', TrainingRequest::STATUS_REJECTED)->count(),
            'total_revenue' => TrainingRequest::where('payment_status', 'paid')->sum('amount_paid'),
        ];


        return response()->json([
            'success' => true,
            'data' => $trainingRequests,
            'statistics' => $statistics,
            'filters' => $request->all()
        ]);
    }

    public function stats()
    {
        $stats = [
            'total' => TrainingRequest::count(),
            'pending' => TrainingRequest::where('status', 'pending')->count(),
            'under_review' => TrainingRequest::where('status', 'under_review')->count(),
            'approved' => TrainingRequest::where('status', 'approved')->count(),
            'completed' => TrainingRequest::where('status', 'completed')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Personal Information
            'full_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',

            // Course Information
            'course_name' => 'nullable|string|max:255',
            'course_id' => 'nullable|exists:training_courses,id',
            'training_course_schedule_id' => 'nullable|exists:training_course_schedules,id',
            'course_code' => 'nullable|string|max:50',
            'training_type' => 'nullable|in:individual,group,company',
            'software' => 'nullable|string|max:100',
            'solution_area' => 'nullable|string|max:100',
            'experience_level' => 'nullable|in:beginner,intermediate,advanced',
            'course_price' => 'nullable|numeric|min:0',

            // Training Preferences
            'preferred_format' => 'nullable|in:online,onsite,hybrid',
            'preferred_start_date' => 'nullable|date|after:today',
            'preferred_timezone' => 'nullable|string|max:100',
            'number_of_participants' => 'nullable|integer|min:1|max:100',

            // Additional Information
            'comments' => 'nullable|string',
            'specific_goals' => 'nullable|string',
            'previous_experience' => 'nullable|string',

            // Tracking
            'source_page' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $trainingRequest = TrainingRequest::create([
                // Personal Information
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'organization' => $request->organization,
                'job_title' => $request->job_title,
                'user_id' => auth()->id(),
                // Course Information
                'course_name' => $request->course_name,
                'course_id' => $request->course_id,
                'training_course_schedule_id' => $request->training_course_schedule_id,
                'course_code' => $request->course_code,
                'training_type' => $request->training_type,
                'software' => $request->software,
                'solution_area' => $request->solution_area,
                'experience_level' => $request->experience_level,
                'course_price' => $request->course_price,

                // Training Preferences
                'preferred_format' => $request->preferred_format,
                'preferred_start_date' => $request->preferred_start_date,
                'preferred_timezone' => $request->preferred_timezone,
                'number_of_participants' => $request->number_of_participants ?? 1,

                // Additional Information
                'comments' => $request->comments,
                'specific_goals' => $request->specific_goals,
                'previous_experience' => $request->previous_experience,

                // Status
                'status' => TrainingRequest::STATUS_APPROVED,
                'payment_status' => $request->course_price > 0 ? 'pending' : 'not_required',

                // Tracking
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'source_page' => $request->source_page,
            ]);

            DB::commit();
            if($request->training_type != 'group'){
              $schedule = TrainingCourseSchedule::create([
                    'training_course_id' => $request->course_id,
                    'date' => $request->preferred_start_date,
                    'training_type' => $request->training_type,
                    'available_seats' => $request->number_of_participants ?? 1,
                ]);
                $trainingRequest->update([
                    'training_course_schedule_id' => $schedule->id,
                 
                ]);
                TrainingEnrollment::create([
                    'training_request_id' => $trainingRequest->id,
                    'training_course_schedule_id' => $schedule->id,
                    // 'status' => 'enrolled',
                    'end_user_id' => auth()->user()->endUser->id,
                    'amount_paid' => $request->course_price ?? 0,
                ]);
            }
            else{
                  TrainingEnrollment::create([
                    'training_request_id' => $trainingRequest->id,
                    'training_course_schedule_id' => $request->training_course_schedule_id,
                    // 'status' => 'enrolled',
                    'end_user_id' => auth()->user()->endUser->id,
                    'amount_paid' => $request->course_price ?? 0,
                ]);
            }
            $trainingRequest->load(['trainingCourseSchedule', 'trainingCourse','trainingEnrollment']);
            // Send notification email (implement this later)
            // Mail::to($trainingRequest->email)->send(new TrainingRequestReceived($trainingRequest));

            return response()->json([
                'success' => true,
                'message' => 'Training request submitted successfully. We will contact you within 2 business days.',
                'data' => $trainingRequest,

            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Training request creation failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit training request. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified training request.
     */
    public function show($id)
    {
        $trainingRequest = TrainingRequest::with(['reviewer', 'user', 'trainingCourseSchedule', 'trainingCourse'])->findOrFail($id);


        return response()->json([
            'success' => true,
            'data' => $trainingRequest
        ]);
    }

    /**
     * Update the specified training request.
     */
    public function update(Request $request, $id)
    {
        $trainingRequest = TrainingRequest::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'full_name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255',
            'phone' => 'nullable|string|max:20',
            'organization' => 'nullable|string|max:255',
            'job_title' => 'nullable|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'course_id' => 'nullable|exists:training_courses,id',
            'training_course_schedule_id' => 'nullable|exists:training_course_schedules,id',
            'course_name' => 'sometimes|string|max:255',
            'course_code' => 'sometimes|string|max:50',
            'training_type' => 'nullable|in:individual,group,company',
            'software' => 'nullable|string|max:100',
            'solution_area' => 'nullable|string|max:100',
            'experience_level' => 'sometimes|in:beginner,intermediate,advanced',
            'course_price' => 'nullable|numeric|min:0',
            'preferred_format' => 'sometimes|in:online,onsite,hybrid',
            'preferred_start_date' => 'nullable|date',
            'number_of_participants' => 'nullable|integer|min:1|max:100',
            'comments' => 'nullable|string',
            'specific_goals' => 'nullable|string',
            'previous_experience' => 'nullable|string',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $trainingRequest->update($request->only([
                'full_name',
                'email',
                'phone',
                'organization',
                'job_title',
                'course_name',
                'course_id',
                'training_course_schedule_id',
                'course_code',
                'training_type',
                'software',
                'solution_area',
                'experience_level',
                'course_price',
                'preferred_format',
                'preferred_start_date',
                'number_of_participants',
                'comments',
                'specific_goals',
                'previous_experience',
                'admin_notes'
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Training request updated successfully',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update training request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of a training request.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,under_review,approved,scheduled,completed,cancelled,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trainingRequest = TrainingRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            $oldStatus = $trainingRequest->status;
            $newStatus = $request->status;

            $updateData = ['status' => $newStatus];

            if ($request->admin_notes) {
                $updateData['admin_notes'] = $request->admin_notes;
            }

            if ($newStatus === TrainingRequest::STATUS_UNDER_REVIEW && !$trainingRequest->reviewed_at) {
                $updateData['reviewed_at'] = now();
                $updateData['reviewed_by'] = auth()->id();
            }

            if ($newStatus === TrainingRequest::STATUS_COMPLETED && !$trainingRequest->completed_at) {
                $updateData['completed_at'] = now();
            }

            if ($newStatus === TrainingRequest::STATUS_CANCELLED || $newStatus === TrainingRequest::STATUS_REJECTED) {
                $updateData['admin_notes'] = $request->admin_notes ?? $trainingRequest->admin_notes;
            }

            $trainingRequest->update($updateData);

            DB::commit();

            // Send status update email
            // Mail::to($trainingRequest->email)->send(new TrainingRequestStatusUpdated($trainingRequest, $oldStatus));

            return response()->json([
                'success' => true,
                'message' => 'Training request status updated successfully',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Schedule a training session.
     */
    public function schedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'scheduled_date' => 'required|date|after:today',
            'scheduled_time' => 'required|date_format:H:i',
            'meeting_link' => 'nullable|url',
            'location' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trainingRequest = TrainingRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            $trainingRequest->schedule(
                $request->scheduled_date,
                $request->scheduled_time,
                $request->meeting_link,
                $request->location
            );

            if ($request->admin_notes) {
                $trainingRequest->admin_notes = $request->admin_notes;
                $trainingRequest->save();
            }

            DB::commit();

            // Send scheduling email
            // Mail::to($trainingRequest->email)->send(new TrainingScheduled($trainingRequest));

            return response()->json([
                'success' => true,
                'message' => 'Training scheduled successfully',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule training',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mark training as completed and issue certificate.
     */
    public function complete(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'feedback' => 'nullable|string',
            'rating' => 'nullable|integer|min:1|max:5',
            'certificate_url' => 'nullable|url',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trainingRequest = TrainingRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            $trainingRequest->complete($request->feedback, $request->rating);

            if ($request->certificate_url) {
                $trainingRequest->issueCertificate($request->certificate_url);
            }

            if ($request->admin_notes) {
                $trainingRequest->admin_notes = $request->admin_notes;
                $trainingRequest->save();
            }

            DB::commit();

            // Send completion email with certificate
            // Mail::to($trainingRequest->email)->send(new TrainingCompleted($trainingRequest));

            return response()->json([
                'success' => true,
                'message' => 'Training marked as completed',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete training',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record payment for a training request.
     */
    public function recordPayment(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_reference' => 'nullable|string|max:255',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trainingRequest = TrainingRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            $trainingRequest->recordPayment($request->amount, $request->payment_reference);

           

            if ($request->admin_notes) {
                $trainingRequest->admin_notes = $request->admin_notes;
                $trainingRequest->save();
            }

            DB::commit();

            // Send payment confirmation email
            // Mail::to($trainingRequest->email)->send(new PaymentReceived($trainingRequest));

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to record payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Issue certificate for completed training.
     */
    public function issueCertificate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'certificate_url' => 'required|url',
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $trainingRequest = TrainingRequest::findOrFail($id);

        if ($trainingRequest->status !== TrainingRequest::STATUS_COMPLETED) {
            return response()->json([
                'success' => false,
                'message' => 'Certificate can only be issued for completed trainings'
            ], 422);
        }

        try {
            DB::beginTransaction();

            $trainingRequest->issueCertificate($request->certificate_url);

            if ($request->admin_notes) {
                $trainingRequest->admin_notes = $request->admin_notes;
                $trainingRequest->save();
            }

            DB::commit();

            // Send certificate email
            // Mail::to($trainingRequest->email)->send(new CertificateIssued($trainingRequest));

            return response()->json([
                'success' => true,
                'message' => 'Certificate issued successfully',
                'data' => $trainingRequest
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to issue certificate',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified training request.
     */
    public function destroy($id)
    {
        $trainingRequest = TrainingRequest::findOrFail($id);

        try {
            DB::beginTransaction();

            $trainingRequest->delete();

            DB::commit();

            if (request()->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Training request deleted successfully'
                ]);
            }

            return redirect()->route('admin.training-requests.index')
                ->with('success', 'Training request deleted successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete training request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete training requests.
     */
    public function bulkDelete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ids' => 'required|array',
            'ids.*' => 'exists:training_requests,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $deleted = TrainingRequest::whereIn('id', $request->ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "{$deleted} training request(s) deleted successfully"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete training requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export training requests to CSV/Excel.
     */
    public function export(Request $request)
    {
        $query = TrainingRequest::query();

        // Apply same filters as index method
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requests = $query->get();

        $filename = 'training-requests-' . Carbon::now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        $callback = function () use ($requests) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'ID',
                'Full Name',
                'Email',
                'Organization',
                'Course Name',
                'Course Code',
                'Training Type',
                'Status',
                'Preferred Start Date',
                'Scheduled Date',
                'Amount Paid',
                'Payment Status',
                'Created At'
            ]);

            // Add data
            foreach ($requests as $request) {
                fputcsv($file, [
                    $request->id,
                    $request->full_name,
                    $request->email,
                    $request->organization,
                    $request->course_name,
                    $request->course_code,
                    $request->training_type,
                    $request->status,
                    $request->preferred_start_date,
                    $request->scheduled_date,
                    $request->amount_paid ?? 0,
                    $request->payment_status,
                    $request->created_at
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get dashboard statistics and charts data.
     */
    public function dashboard()
    {
        // Get statistics for the last 12 months
        $monthlyRequests = TrainingRequest::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as total')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Get popular software requests
        $popularSoftware = TrainingRequest::select('software', DB::raw('COUNT(*) as total'))
            ->whereNotNull('software')
            ->groupBy('software')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();

        // Get training type distribution
        $trainingTypeDistribution = TrainingRequest::select('training_type', DB::raw('COUNT(*) as total'))
            ->groupBy('training_type')
            ->get();

        // Get status distribution
        $statusDistribution = TrainingRequest::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get();

        // Get revenue by month
        $monthlyRevenue = TrainingRequest::where('payment_status', 'paid')
            ->select(
                DB::raw('MONTH(paid_at) as month'),
                DB::raw('YEAR(paid_at) as year'),
                DB::raw('SUM(amount_paid) as total')
            )
            ->where('paid_at', '>=', Carbon::now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'monthly_requests' => $monthlyRequests,
                'popular_software' => $popularSoftware,
                'training_type_distribution' => $trainingTypeDistribution,
                'status_distribution' => $statusDistribution,
                'monthly_revenue' => $monthlyRevenue,
            ]
        ]);
    }

    /**
     * Get analytics and reports.
     */
    public function reports(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subMonth());
        $endDate = $request->get('end_date', Carbon::now());

        $reports = [
            'total_requests' => TrainingRequest::whereBetween('created_at', [$startDate, $endDate])->count(),
            'completion_rate' => $this->calculateCompletionRate($startDate, $endDate),
            'average_response_time' => $this->calculateAverageResponseTime($startDate, $endDate),
            'revenue' => TrainingRequest::whereBetween('paid_at', [$startDate, $endDate])
                ->where('payment_status', 'paid')
                ->sum('amount_paid'),
            'top_courses' => $this->getTopCourses($startDate, $endDate),
            'top_organizations' => $this->getTopOrganizations($startDate, $endDate),
        ];

        return response()->json([
            'success' => true,
            'data' => $reports,
            'period' => [
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    /**
     * Helper method to calculate completion rate.
     */
    private function calculateCompletionRate($startDate, $endDate)
    {
        $total = TrainingRequest::whereBetween('created_at', [$startDate, $endDate])->count();
        $completed = TrainingRequest::whereBetween('completed_at', [$startDate, $endDate])
            ->where('status', TrainingRequest::STATUS_COMPLETED)
            ->count();

        return $total > 0 ? round(($completed / $total) * 100, 2) : 0;
    }

    /**
     * Helper method to calculate average response time.
     */
    private function calculateAverageResponseTime($startDate, $endDate)
    {
        $avgTime = TrainingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('reviewed_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, created_at, reviewed_at)) as avg_hours'))
            ->first();

        return $avgTime ? round($avgTime->avg_hours, 2) : 0;
    }

    /**
     * Helper method to get top courses.
     */
    private function getTopCourses($startDate, $endDate)
    {
        return TrainingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select('course_name', 'course_code', DB::raw('COUNT(*) as total'))
            ->groupBy('course_name', 'course_code')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Helper method to get top organizations.
     */
    private function getTopOrganizations($startDate, $endDate)
    {
        return TrainingRequest::whereBetween('created_at', [$startDate, $endDate])
            ->select('organization', DB::raw('COUNT(*) as total'))
            ->whereNotNull('organization')
            ->groupBy('organization')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Get all available software for filtering.
     */
    public function getSoftwareList()
    {
        $software = TrainingRequest::select('software')
            ->whereNotNull('software')
            ->distinct()
            ->pluck('software');

        return response()->json([
            'success' => true,
            'data' => $software
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\TrainerPreferdSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainerPreferdScheduleController extends Controller
{
        public function index(Request $request)
    {
        try {
            $query = TrainerPreferdSchedule::with(['trainer', 'trainerRequestForm']);
            
            // Filter by trainer_id if provided
            if ($request->has('trainer_id')) {
                $query->where('trainer_id', $request->trainer_id);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            $schedules = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'data' => $schedules,
                'message' => 'Schedules retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedules',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created trainer preferred schedule
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trainer_id' => 'nullable|exists:users,id',
                'trainer_request_form_id' => 'nullable|exists:trainer_request_forms,id',
                'days' => 'required|array',
                'days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'status' => 'sometimes|integer|in:0,1',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $schedule = TrainerPreferdSchedule::create([
                'trainer_id' => $request->trainer_id,
                'trainer_request_form_id' => $request->trainer_request_form_id,
                'days' => json_encode($request->days),
                'status' => $request->status ?? 0,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $schedule->load(['trainer', 'trainerRequestForm']),
                'message' => 'Schedule created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified trainer preferred schedule
     */
    public function show($id)
    {
        try {
            $schedule = TrainerPreferdSchedule::with(['trainer', 'trainerRequestForm'])->find($id);
            
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
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the specified trainer preferred schedule
     */
    public function update(Request $request, $id)
    {
        try {
            $schedule = TrainerPreferdSchedule::find($id);
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'trainer_id' => 'nullable|exists:users,id',
                'trainer_request_form_id' => 'nullable|exists:trainer_request_forms,id',
                'days' => 'sometimes|array',
                'days.*' => 'string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
                'status' => 'sometimes|integer|in:0,1',
                'start_date' => 'sometimes|date',
                'end_date' => 'sometimes|date|after_or_equal:start_date',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update only provided fields
            if ($request->has('trainer_id')) $schedule->trainer_id = $request->trainer_id;
            if ($request->has('trainer_request_form_id')) $schedule->trainer_request_form_id = $request->trainer_request_form_id;
            if ($request->has('days')) $schedule->days = json_encode($request->days);
            if ($request->has('status')) $schedule->status = $request->status;
            if ($request->has('start_date')) $schedule->start_date = $request->start_date;
            if ($request->has('end_date')) $schedule->end_date = $request->end_date;
            
            $schedule->save();
            
            return response()->json([
                'success' => true,
                'data' => $schedule->load(['trainer', 'trainerRequestForm']),
                'message' => 'Schedule updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified trainer preferred schedule
     */
    public function destroy($id)
    {
        try {
            $schedule = TrainerPreferdSchedule::find($id);
            
            if (!$schedule) {
                return response()->json([
                    'success' => false,
                    'message' => 'Schedule not found'
                ], 404);
            }
            
            $schedule->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Schedule deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}

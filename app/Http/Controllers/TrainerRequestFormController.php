<?php

namespace App\Http\Controllers;

use App\Models\TrainerRequestForm;
use App\Models\TrainerPreferredSchedule;
use App\Models\TrainerSkill;
use App\Http\Requests\TrainerRequestFormRequest;
use App\Http\Resources\TrainerRequestFormResource;
use App\Models\Trainer;
use App\Models\TrainerCourse;
use App\Models\TrainerPreferdSchedule;
use App\Models\TrainerSchedule;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TrainerRequestFormController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainerRequestForm::with(['schedules', 'skills','courses.trainingCourse', 'skills.software', 'skills.solution']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('current_company')) {
            $query->where('current_company', 'LIKE', '%' . $request->current_company . '%');
        }

        if ($request->has('current_position')) {
            $query->where('current_position', 'LIKE', '%' . $request->current_position . '%');
        }

        if ($request->has('experience_year')) {
            $query->where('experience_year', $request->experience_year);
        }
        
        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainerRequestFormResource::collection($lists);
    }

    public function store(TrainerRequestFormRequest $request)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validated();
            
            // Create trainer request form
            $trainerRequestForm = TrainerRequestForm::create($data);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('uploads/trainerRequestForm', 'public');
                $trainerRequestForm->update(['image' => $path]);
            }
            if($request->course_ids && is_array($request->course_ids)){
                foreach($request->course_ids as $course_id){
                    TrainerCourse::create([
                        'trainer_request_form_id' => $trainerRequestForm->id,
                        'training_course_id' => $course_id,
                        'status' => 0,
                    ]);
                }
            }
            // Create schedules if provided
            if ($request->has('schedules') && is_array($request->schedules)) {
                foreach ($request->schedules as $schedule) {
                    TrainerPreferdSchedule::create([
                        'trainer_request_form_id' => $trainerRequestForm->id,
                        'trainer_id' => $data['trainer_id'] ?? null,
                        'days' => json_encode($schedule['days']),
                        'start_time' => $schedule['start_time'],
                        'end_time' => $schedule['end_time'],
                        'status' => $schedule['status'] ?? 0,
                    ]);
                }
            }
            
            // Create skills if provided
            if ($request->has('skills') && is_array($request->skills)) {
                foreach ($request->skills as $skill) {
                    TrainerSkill::create([
                        'trainer_request_form_id' => $trainerRequestForm->id,
                        'trainer_id' => $data['trainer_id'] ?? null,
                        'skill_type' => $skill['skill_type'] ?? 0,
                        'software_id' => $skill['software_id'] ?? null,
                        'level' => $skill['level'] ?? null,
                        'solution_id' => $skill['solution_id'] ?? null,
                        'analysis' => $skill['analysis'] ?? null,
                        'status' => $skill['status'] ?? 0,
                    ]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Trainer request form created successfully with schedules and skills',
                'data' => $trainerRequestForm->load(['schedules', 'skills']),
            ], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to create trainer request form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(TrainerRequestForm $trainerRequestForm)
    {
        return new TrainerRequestFormResource($trainerRequestForm->load(['schedules', 'skills', 'skills.software', 'skills.solution']));
    }

    public function update(Request $request, TrainerRequestForm $trainerRequestForm)
    {
        try {
            DB::beginTransaction();
            
            $data = $request->validate([
                'trainer_id' => 'nullable|exists:users,id',
                'industry_id' => 'nullable|exists:industries,id',
                'solution_id' => 'nullable|exists:solutions,id',
                'software_id' => 'nullable|exists:softwares,id',
                'name' => 'nullable|string|max:255',
                'email' => 'nullable|email|unique:trainer_request_forms,email,' . $trainerRequestForm->id,
                'phone' => 'nullable|string|unique:trainer_request_forms,phone,' . $trainerRequestForm->id,
                'address' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'experience_year' => 'nullable|string',
                'current_company' => 'nullable|string',
                'current_position' => 'nullable|string',
                'status' => 'nullable|integer',
                
                // Schedules data
                'schedules' => 'nullable|array',
                'schedules.*.days' => 'required|array',
                'schedules.*.start_date' => 'required|date',
                'schedules.*.end_date' => 'required|date|after_or_equal:schedules.*.start_date',
                'schedules.*.status' => 'nullable|integer|in:0,1',
                
                // Skills data
                'skills' => 'nullable|array',
                'skills.*.skill_type' => 'nullable|integer',
                'skills.*.software_id' => 'nullable|exists:softwares,id',
                'skills.*.level' => 'nullable|string|max:255',
                'skills.*.solution_id' => 'nullable|exists:solutions,id',
                'skills.*.analysis' => 'nullable|string',
                'skills.*.status' => 'nullable|integer|in:0,1',
                
                // For updating existing records
                'schedule_ids_to_delete' => 'nullable|array',
                'skill_ids_to_delete' => 'nullable|array',
            ]);
            
            // Handle image upload
            if ($request->hasFile('image')) {
                if ($trainerRequestForm->image && Storage::disk('public')->exists($trainerRequestForm->image)) {
                    Storage::disk('public')->delete($trainerRequestForm->image);
                }
                $path = $request->file('image')->store('uploads/trainerRequestForm', 'public');
                $data['image'] = $path;
            }
            
            // Update main form
            $trainerRequestForm->update($data);
            
            // Delete schedules if specified
            if ($request->has('schedule_ids_to_delete')) {
                TrainerPreferdSchedule::whereIn('id', $request->schedule_ids_to_delete)
                    ->where('trainer_request_form_id', $trainerRequestForm->id)
                    ->delete();
            }
            
            // Update or create schedules
            if ($request->has('schedules')) {
                foreach ($request->schedules as $scheduleData) {
                    if (isset($scheduleData['id'])) {
                        // Update existing schedule
                        $schedule = TrainerPreferdSchedule::where('id', $scheduleData['id'])
                            ->where('trainer_request_form_id', $trainerRequestForm->id)
                            ->first();
                        if ($schedule) {
                            $schedule->update([
                                'days' => json_encode($scheduleData['days']),
                                'start_date' => $scheduleData['start_date'],
                                'end_date' => $scheduleData['end_date'],
                                'status' => $scheduleData['status'] ?? $schedule->status,
                            ]);
                        }
                    } else {
                        // Create new schedule
                        TrainerPreferdSchedule::create([
                            'trainer_request_form_id' => $trainerRequestForm->id,
                            'trainer_id' => $data['trainer_id'] ?? null,
                            'days' => json_encode($scheduleData['days']),
                            'start_date' => $scheduleData['start_date'],
                            'end_date' => $scheduleData['end_date'],
                            'status' => $scheduleData['status'] ?? 0,
                        ]);
                    }
                }
            }
            
            // Delete skills if specified
            if ($request->has('skill_ids_to_delete')) {
                TrainerSkill::whereIn('id', $request->skill_ids_to_delete)
                    ->where('trainer_request_form_id', $trainerRequestForm->id)
                    ->delete();
            }
            
            // Update or create skills
            if ($request->has('skills')) {
                foreach ($request->skills as $skillData) {
                    if (isset($skillData['id'])) {
                        // Update existing skill
                        $skill = TrainerSkill::where('id', $skillData['id'])
                            ->where('trainer_request_form_id', $trainerRequestForm->id)
                            ->first();
                        if ($skill) {
                            $skill->update([
                                'skill_type' => $skillData['skill_type'] ?? $skill->skill_type,
                                'software_id' => $skillData['software_id'] ?? $skill->software_id,
                                'level' => $skillData['level'] ?? $skill->level,
                                'solution_id' => $skillData['solution_id'] ?? $skill->solution_id,
                                'analysis' => $skillData['analysis'] ?? $skill->analysis,
                                'status' => $skillData['status'] ?? $skill->status,
                            ]);
                        }
                    } else {
                        // Create new skill
                        TrainerSkill::create([
                            'trainer_request_form_id' => $trainerRequestForm->id,
                            'trainer_id' => $data['trainer_id'] ?? null,
                            'skill_type' => $skillData['skill_type'] ?? 0,
                            'software_id' => $skillData['software_id'] ?? null,
                            'level' => $skillData['level'] ?? null,
                            'solution_id' => $skillData['solution_id'] ?? null,
                            'analysis' => $skillData['analysis'] ?? null,
                            'status' => $skillData['status'] ?? 0,
                        ]);
                    }
                }
            }
            
            DB::commit();
            
            return response()->json([
                'status' => true,
                'message' => 'Trainer request form updated successfully',
                'data' => $trainerRequestForm->load(['schedules', 'skills']),
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update trainer request form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(TrainerRequestForm $trainerRequestForm)
    {
        try {
            DB::beginTransaction();
            
            // Delete related schedules and skills (cascade should handle this if set in migrations)
            $trainerRequestForm->schedules()->delete();
            $trainerRequestForm->skills()->delete();
            
            // Delete image if exists
            if ($trainerRequestForm->image && Storage::disk('public')->exists($trainerRequestForm->image)) {
                Storage::disk('public')->delete($trainerRequestForm->image);
            }
            
            // Delete the main form
            $trainerRequestForm->delete();
            
            DB::commit();
            
            return response()->json([
                'status' => true, 
                'message' => 'Trainer request form and all related data deleted successfully'
            ], 200);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete trainer request form',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function statusUpdate(Request $request, $id)
    {
        $trainerRequestForm = TrainerRequestForm::findOrFail($id);
        $trainerRequestForm->update(['status' => $request->status]);
        
        return response()->json([
            'status' => true, 
            'message' => 'Trainer request form status updated successfully'
        ], 200);
    }
    /**
 * Approve the trainer request and create trainer account
 */
public function approve($id)
{
    try {
        DB::beginTransaction();
        
        $trainerRequestForm = TrainerRequestForm::findOrFail($id);
        
        // Check if already approved
        if ($trainerRequestForm->status == 1) {
            return response()->json([
                'status' => false,
                'message' => 'Trainer request already approved'
            ], 400);
        }
        
        // Check if trainer already exists
        if ($trainerRequestForm->trainer_id) {
            return response()->json([
                'status' => false,
                'message' => 'Trainer already exists for this request'
            ], 400);
        }
        
        // Validate required fields
        if (!$trainerRequestForm->email || !$trainerRequestForm->name) {
            return response()->json([
                'status' => false,
                'message' => 'Missing required trainer information (name or email)'
            ], 422);
        }
        
        // Create user account
        $user = User::create([
            'name' => $trainerRequestForm->name,
            'email' => $trainerRequestForm->email,
            'password' => Hash::make('12345678'), // Default password
            'role' => 'trainer',
            'status' => 1,
            'email_verified_at' => now(),
        ]);
        
        // Create trainer profile
        $trainer = Trainer::create([
            'user_id' => $user->id,
            'industry_id' => $trainerRequestForm->industry_id,
            'solution_id' => $trainerRequestForm->solution_id,
            'software_id' => $trainerRequestForm->software_id,
            'experience_year' => $trainerRequestForm->experience_year,
            'current_company' => $trainerRequestForm->current_company,
            'current_position' => $trainerRequestForm->current_position,
            'status' => 1,
        ]);
        
        // Copy image
        if ($trainerRequestForm->image) {
            $extension = pathinfo($trainerRequestForm->image, PATHINFO_EXTENSION);
            $newImageName = 'trainer_' . $user->id . '_' . time() . '.' . $extension;
            $newPath = 'uploads/trainer/' . $newImageName;
            Storage::disk('public')->copy($trainerRequestForm->image, $newPath);
            $trainer->update(['image' => $newPath]);
        }
        
        // Link trainer to request form
        $trainerRequestForm->update([
            'status' => 1,
            'trainer_id' => $user->id
        ]);
        
        // Update schedules with trainer_id
        TrainerPreferdSchedule::where('trainer_request_form_id', $trainerRequestForm->id)
            ->update(['trainer_id' => $user->id]);
        
        // Update skills with trainer_id
        TrainerSkill::where('trainer_request_form_id', $trainerRequestForm->id)
            ->update(['trainer_id' => $user->id]);
        TrainerCourse::where('trainer_request_form_id', $trainerRequestForm->id)
            ->update(['trainer_id' => $user->id]);
        DB::commit();
        
        return response()->json([
            'status' => true,
            'message' => 'Trainer request approved and trainer account created successfully',
            'data' => [
                'trainer_request_form' => $trainerRequestForm,
                'user' => $user,
                'trainer' => $trainer,
                'credentials' => [
                    'email' => $user->email,
                    'default_password' => '12345678'
                ]
            ]
        ], 200);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status' => false,
            'message' => 'Failed to approve trainer request',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Reject the trainer request
 */
public function reject($id)
{
    try {
        $trainerRequestForm = TrainerRequestForm::findOrFail($id);
        
        $trainerRequestForm->update(['status' => 2]); // Assuming 2 = rejected
        
        return response()->json([
            'status' => true,
            'message' => 'Trainer request rejected successfully'
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Failed to reject trainer request',
            'error' => $e->getMessage()
        ], 500);
    }
}
    // Additional helper methods for managing schedules and skills independently
    
    /**
     * Add schedule to existing request form
     */
    public function addSchedule(Request $request, $id)
    {
        try {
            $trainerRequestForm = TrainerRequestForm::findOrFail($id);
            
            $request->validate([
                'days' => 'required|array',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'status' => 'nullable|integer|in:0,1',
            ]);
            
            $schedule = TrainerPreferdSchedule::create([
                'trainer_request_form_id' => $trainerRequestForm->id,
                'trainer_id' => $trainerRequestForm->trainer_id,
                'days' => json_encode($request->days),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'status' => $request->status ?? 0,
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Schedule added successfully',
                'data' => $schedule
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add schedule',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Add skill to existing request form
     */
    public function addSkill(Request $request, $id)
    {
        try {
            $trainerRequestForm = TrainerRequestForm::findOrFail($id);
            
            $request->validate([
                'skill_type' => 'nullable|integer',
                'software_id' => 'nullable|exists:softwares,id',
                'level' => 'nullable|string|max:255',
                'solution_id' => 'nullable|exists:solutions,id',
                'analysis' => 'nullable|string',
                'status' => 'nullable|integer|in:0,1',
            ]);
            
            $skill = TrainerSkill::create([
                'trainer_request_form_id' => $trainerRequestForm->id,
                'trainer_id' => $trainerRequestForm->trainer_id,
                'skill_type' => $request->skill_type ?? 0,
                'software_id' => $request->software_id,
                'level' => $request->level,
                'solution_id' => $request->solution_id,
                'analysis' => $request->analysis,
                'status' => $request->status ?? 0,
            ]);
            
            return response()->json([
                'status' => true,
                'message' => 'Skill added successfully',
                'data' => $skill->load('software', 'solution')
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to add skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
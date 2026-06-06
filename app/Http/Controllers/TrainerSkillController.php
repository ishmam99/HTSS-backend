<?php

namespace App\Http\Controllers;

use App\Models\TrainerSkill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TrainerSkillController extends Controller
{
    
  
    public function index(Request $request)
    {
        try {
            $query = TrainerSkill::with(['trainer', 'trainerRequestForm', 'software', 'solution']);
            
            // Filter by trainer_id if provided
            if ($request->has('trainer_id')) {
                $query->where('trainer_id', $request->trainer_id);
            }
            
            // Filter by skill_type if provided
            if ($request->has('skill_type')) {
                $query->where('skill_type', $request->skill_type);
            }
            
            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }
            
            // Filter by software_id if provided
            if ($request->has('software_id')) {
                $query->where('software_id', $request->software_id);
            }
            
            // Filter by solution_id if provided
            if ($request->has('solution_id')) {
                $query->where('solution_id', $request->solution_id);
            }
            
            $skills = $query->paginate($request->get('per_page', 15));
            
            return response()->json([
                'success' => true,
                'data' => $skills,
                'message' => 'Skills retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve skills',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Store a newly created trainer skill
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trainer_id' => 'nullable|exists:users,id',
                'trainer_request_form_id' => 'nullable|exists:trainer_request_forms,id',
                'skill_type' => 'sometimes|integer|in:0,1,2', // Adjust based on your enum values
                'software_id' => 'nullable|exists:softwares,id',
                'level' => 'nullable|string|max:255',
                'solution_id' => 'nullable|exists:solutions,id',
                'analysis' => 'nullable|string',
                'status' => 'sometimes|integer|in:0,1',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            $skill = TrainerSkill::create([
                'trainer_id' =>auth()->id(),
                'trainer_request_form_id' => $request->trainer_request_form_id,
                'skill_type' => $request->skill_type ?? 0,
                'software_id' => $request->software_id,
                'level' => $request->level,
                'solution_id' => $request->solution_id,
                'analysis' => $request->analysis,
                'status' => $request->status ?? 0,
            ]);
            
            return response()->json([
                'success' => true,
                'data' => $skill->load(['trainer', 'trainerRequestForm', 'software', 'solution']),
                'message' => 'Skill created successfully'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified trainer skill
     */
    public function show($id)
    {
        try {
            $skill = TrainerSkill::with(['trainer', 'trainerRequestForm', 'software', 'solution'])->find($id);
            
            if (!$skill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Skill not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $skill,
                'message' => 'Skill retrieved successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the specified trainer skill
     */
    public function update(Request $request, $id)
    {
        try {
            $skill = TrainerSkill::find($id);
            
            if (!$skill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Skill not found'
                ], 404);
            }
            
            $validator = Validator::make($request->all(), [
                'trainer_id' => 'nullable|exists:users,id',
                'trainer_request_form_id' => 'nullable|exists:trainer_request_forms,id',
                'skill_type' => 'sometimes|integer|in:0,1,2',
                'software_id' => 'nullable|exists:softwares,id',
                'level' => 'nullable|string|max:255',
                'solution_id' => 'nullable|exists:solutions,id',
                'analysis' => 'nullable|string',
                'status' => 'sometimes|integer|in:0,1',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update only provided fields
            if ($request->has('trainer_id')) $skill->trainer_id = $request->trainer_id;
            if ($request->has('trainer_request_form_id')) $skill->trainer_request_form_id = $request->trainer_request_form_id;
            if ($request->has('skill_type')) $skill->skill_type = $request->skill_type;
            if ($request->has('software_id')) $skill->software_id = $request->software_id;
            if ($request->has('level')) $skill->level = $request->level;
            if ($request->has('solution_id')) $skill->solution_id = $request->solution_id;
            if ($request->has('analysis')) $skill->analysis = $request->analysis;
            if ($request->has('status')) $skill->status = $request->status;
            
            $skill->save();
            
            return response()->json([
                'success' => true,
                'data' => $skill->load(['trainer', 'trainerRequestForm', 'software', 'solution']),
                'message' => 'Skill updated successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove the specified trainer skill
     */
    public function destroy($id)
    {
        try {
            $skill = TrainerSkill::find($id);
            
            if (!$skill) {
                return response()->json([
                    'success' => false,
                    'message' => 'Skill not found'
                ], 404);
            }
            
            $skill->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Skill deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete skill',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

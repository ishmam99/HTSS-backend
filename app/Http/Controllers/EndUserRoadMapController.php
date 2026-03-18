<?php

namespace App\Http\Controllers;

use App\Models\EndUserRoadMap;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EndUserRoadMapController extends Controller
{
    // List all roadmap items for the authenticated user
    public function index(Request $request)
    {
        $userId = auth()->user()->endUser->id;


         $query = EndUserRoadMap::advancedQuery($request);
        $query = $query->where('end_user_id', $userId);
    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

     return response()->json([
            'success' => true,
            'data' => $lists,
          
        ]);
    }

    // Show a single roadmap item
    public function show($id)
    {
        $userId = auth()->user()->endUser->id;

        $roadMap = EndUserRoadMap::where('id', $id)
            ->where('end_user_id', $userId)
            ->with(['trainingCourse', 'software', 'solution'])
            ->firstOrFail();

        return response()->json($roadMap);
    }

    // Store a new roadmap item
    public function store(Request $request)
    {
        $userId = auth()->user()->endUser->id;

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'training_course_id' => 'nullable|exists:training_courses,id',
            'software_id' => 'nullable|exists:softwares,id',
            'solution_id' => 'nullable|exists:solutions,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => ['nullable', Rule::in(['planned', 'in_progress', 'completed', 'paused'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'is_reminder_enabled' => 'nullable|boolean',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
        ]);

        $roadMap = EndUserRoadMap::create(array_merge($validated, [
            'end_user_id' => $userId,
        ]));

        return response()->json($roadMap, 201);
    }

    // Update an existing roadmap item
    public function update(Request $request, $id)
    {
        $userId = auth()->user()->endUser->id;

        $roadMap = EndUserRoadMap::where('id', $id)
            ->where('end_user_id', $userId)
            ->firstOrFail();

        $validated = $request->validate([
            'type' => 'sometimes|string|max:255',
            'training_course_id' => 'nullable|exists:training_courses,id',
            'software_id' => 'nullable|exists:softwares,id',
            'solution_id' => 'nullable|exists:solutions,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'progress' => 'nullable|integer|min:0|max:100',
            'status' => ['nullable', Rule::in(['planned', 'in_progress', 'completed', 'paused'])],
            'priority' => ['nullable', Rule::in(['low', 'medium', 'high'])],
            'is_reminder_enabled' => 'nullable|boolean',
            'estimated_hours' => 'nullable|integer|min:0',
            'actual_hours' => 'nullable|integer|min:0',
        ]);

        $roadMap->update($validated);

        return response()->json($roadMap);
    }

    // Delete a roadmap item
    public function destroy($id)
    {
        $userId = auth()->user()->endUser->id;

        $roadMap = EndUserRoadMap::where('id', $id)
            ->where('end_user_id', $userId)
            ->firstOrFail();

        $roadMap->delete();

        return response()->json(['message' => 'Roadmap item deleted successfully']);
    }
}
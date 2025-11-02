<?php

namespace App\Http\Controllers;

use App\Models\SolutionTraining;
use Illuminate\Http\Request;
use App\Http\Resources\SolutionTrainingResource;

class SolutionTrainingController extends Controller
{
    public function index(Request $request)
    {
        $solutionTrainings = SolutionTraining::with('trainingSchedule')->paginate(10);
        return SolutionTrainingResource::collection($solutionTrainings);
    }

    public function show($id)
    {
        $solutionTraining = SolutionTraining::with('trainingSchedule')->findOrFail($id);
        return new SolutionTrainingResource($solutionTraining);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_schedule_id' => 'required|exists:training_schedules,id',
            'title' => 'required|string',
            'objective' => 'nullable|string',
            'content' => 'nullable|string',
            'material_link' => 'nullable|string',
            'duration_minutes' => 'nullable|integer',
            'level' => 'nullable|integer|in:1,2,3,4',
        ]);

        $solutionTraining = SolutionTraining::create($validated);
        $solutionTraining->load('trainingSchedule');

        return new SolutionTrainingResource($solutionTraining);
    }

    public function update(Request $request, $id)
    {
        $solutionTraining = SolutionTraining::findOrFail($id);

        $validated = $request->validate([
            'title' => 'nullable|string',
            'objective' => 'nullable|string',
            'content' => 'nullable|string',
            'material_link' => 'nullable|string',
            'duration_minutes' => 'nullable|integer',
            'level' => 'nullable|integer|in:1,2,3',
            'status' => 'nullable|integer',
        ]);

        $solutionTraining->update($validated);
        $solutionTraining->load('trainingSchedule');

        return new SolutionTrainingResource($solutionTraining);
    }
    public function destroy($id)
    {
        $solutionTraining = SolutionTraining::findOrFail($id);
        $solutionTraining->delete();

        return response()->json(['message' => 'Solution training deleted successfully']);
    }
}

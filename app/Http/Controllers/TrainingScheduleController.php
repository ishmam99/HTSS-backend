<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainingSchedule;
use App\Http\Resources\TrainingScheduleResource;

class TrainingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $schedules = TrainingSchedule::paginate(10);
        return TrainingScheduleResource::collection($schedules);
    }

    public function show($id)
    {
        $schedule = TrainingSchedule::findOrFail($id);
        return new TrainingScheduleResource($schedule);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
        ]);

        $schedule = TrainingSchedule::create($validated);

        return new TrainingScheduleResource($schedule);
    }

    public function update(Request $request, $id)
    {
        $schedule = TrainingSchedule::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'location' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'status' => 'nullable|integer',
        ]);

        $schedule->update($validated);

        return new TrainingScheduleResource($schedule);
    }

    public function destroy($id)
    {
        $schedule = TrainingSchedule::findOrFail($id);
        $schedule->delete();

        return response()->json(['message' => 'Training schedule deleted successfully']);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TrainingSchedule;
use Illuminate\Http\Request;
use App\Http\Requests\TrainingScheduleRequest;
use App\Http\Resources\TrainingScheduleResource;

class TrainingScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingSchedule::query()
            ->when($request->training_course_id, function ($query, $trainingCourseId) {
                return $query->where('training_course_id', $trainingCourseId);
            })
            ->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        $lists->load('trainingCourse');

        return TrainingScheduleResource::collection($lists);
    }

    public function store(TrainingScheduleRequest $request)
    {
        $data = $request->validated();

        TrainingSchedule::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Training Schedule created successfully',
        ], 201);
    }

    public function show(TrainingSchedule $trainingSchedule)
    {
        $trainingSchedule->load('trainingCourse');

        return new TrainingScheduleResource($trainingSchedule);
    }

    public function update(
        TrainingScheduleRequest $request,
        TrainingSchedule $trainingSchedule
    ) {
        $data = $request->validated();

        $trainingSchedule->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Training Schedule updated successfully',
        ], 200);
    }

    public function destroy(TrainingSchedule $trainingSchedule)
    {
        $trainingSchedule->delete();

        return response()->json([
            'status' => true,
            'message' => 'Training Schedule deleted successfully',
        ], 200);
    }
}

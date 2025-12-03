<?php

namespace App\Http\Controllers;

use App\Models\TrainerCourse;
use App\Http\Requests\TrainerCourseRequest;
use App\Http\Resources\TrainerCourseResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainerCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainerCourse::with(['trainingCourse.software','trainingCourse.solution','trainingCourse.industry'])->when($request->has('status'), function ($query) use ($request) {
            return $query->where('status', $request->status);
        })->when($request->trainer_id, function ($query) use ($request) {
            return $query->where('trainer_id', $request->trainer_id);
        })->when($request->training_course_id, function ($query) use ($request) {
            return $query->where('training_course_id', $request->training_course_id);
        })->orderBy('id', 'desc');

        if(auth()->user()->role == 'trainer')
        {
             $query =  $query->where('trainer_id',auth()->id());
        }

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainerCourseResource::collection($lists);
    }


    public function store(TrainerCourseRequest $request)
    {
        $items = $request->validated()['items'];
        $trainerId = auth()->id();

            foreach ($items as $item) {
            TrainerCourse::create([
                'training_course_id' => $item['training_course_id'],
                'status' => $item['status'] ?? 0,
                'trainer_id' => $trainerId,
            ]);
        }

    return response()->json([
        'status' => true,
        'message' => 'TrainerCourse created successfully',
    ], 201);
    }

    public function show(TrainerCourse $trainerCourse)
    {
        return new TrainerCourseResource($trainerCourse);
    }

    public function update(Request $request, TrainerCourse $trainerCourse)
    {
        $data = $request->validated();

        $trainerCourse->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainerCourse updated successfully',
        ], 200);
    }

    public function destroy(TrainerCourse $trainerCourse)
    {
        $trainerCourse->delete();
        return response()->json(['status' => true, 'message' => 'TrainerCourse deleted successfully'], 200);
    }

    public function statusUpdate(Request $request,$id)
    {
       $trainerCourse = TrainerCourse::find($id);
       $trainerCourse->update([
            'status' => $request->status
       ]);

        return response()->json([
            'status' => true,
            'message' => 'TrainerCourse status updated successfully',
        ], 200);
    }
}

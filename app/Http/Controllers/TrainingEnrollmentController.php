<?php

namespace App\Http\Controllers;

use App\Models\TrainingEnrollment;
use App\Http\Requests\TrainingEnrollmentRequest;
use App\Http\Resources\TrainingEnrollmentResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainingEnrollmentController extends Controller
{
    public function index(Request $request)
    {
        $query = TrainingEnrollment::with(['endUser', 'trainingRequest', 'trainingCourseSchedule'])->when($request->status, function ($query, $status) {
            return $query->where('status', $status);
        })->when($request->end_user_id, function ($q, $endUserId) {
            return $q->where('end_user_id', $endUserId);
        })
            ->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainingEnrollmentResource::collection($lists);
    }


    public function store(TrainingEnrollmentRequest $request)
    {
        $data = $request->validated();

        $trainingEnrollment = TrainingEnrollment::create($data);


        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/trainingEnrollment', 'public');
            $trainingEnrollment->update(['image' => $path]);
        }


        return response()->json([
            'status' => true,
            'message' => 'TrainingEnrollment created successfully',
        ], 201);
    }

    public function show(TrainingEnrollment $trainingEnrollment)
    {
        return new TrainingEnrollmentResource($trainingEnrollment);
    }

    public function update(TrainingEnrollmentRequest $request, TrainingEnrollment $trainingEnrollment)
    {
        $data = $request->validated();


        if ($request->hasFile('image')) {

            if ($trainingEnrollment->image && Storage::disk('public')->exists($trainingEnrollment->image)) {
                Storage::disk('public')->delete($trainingEnrollment->image);
            }


            $path = $request->file('image')->store('uploads/trainingEnrollment', 'public');
            $data['image'] = $path;
        }


        $trainingEnrollment->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainingEnrollment updated successfully',
        ], 200);
    }

    public function destroy(TrainingEnrollment $trainingEnrollment)
    {
        $trainingEnrollment->delete();
        return response()->json(['status' => true, 'message' => 'TrainingEnrollment deleted successfully'], 200);
    }
}

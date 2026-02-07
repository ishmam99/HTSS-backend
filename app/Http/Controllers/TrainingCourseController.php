<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use App\Http\Requests\TrainingCourseRequest;
use App\Http\Resources\TrainingCourseResource;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class TrainingCourseController extends Controller
{
  public function index(Request $request)
{
    $query = TrainingCourse::advancedQuery($request);
    $lists = $request->per_page
        ? $query->paginate($request->per_page)
        : $query->get();

    return response()->json([
            'success' => true,
            'data' => $lists,
             'total' => TrainingCourse::count()
        ]);
}



    public function store(TrainingCourseRequest $request)
    {
        $data = $request->validated();
       $user = auth()->user();
       if($user->role == 'customer'){
        $data['customer_id'] = $user->customer->id;
       }
        $trainingCourse = TrainingCourse::create($data);
        return response()->json([
            'status' => true,
            'message' => 'TrainingCourse created successfully',
        ], 201);
    }

    public function show(TrainingCourse $trainingCourse)
    {
        return new TrainingCourseResource($trainingCourse);
    }

    public function update(TrainingCourseRequest $request, TrainingCourse $trainingCourse)
    {
        $data = $request->validated();
        $trainingCourse->update($data);

        return response()->json([
            'status' => true,
            'message' => 'TrainingCourse updated successfully',
        ], 200);
    }

    public function destroy(TrainingCourse $trainingCourse)
    {
        $trainingCourse->delete();
        return response()->json(['status' => true,'message' => 'TrainingCourse deleted successfully'],200);
    }

    public function getByCompany($companyId)
{
    $courses = TrainingCourse::with(['customer','solution','software','industry'])
        ->whereHas('customer', function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
        })
        ->get();

    return response()->json([
        'success' => true,
        'data' => $courses
    ]);
}
}

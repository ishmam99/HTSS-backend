<?php

namespace App\Http\Controllers;

use App\Models\TrainingEnrollment;
use Illuminate\Http\Request;

class TrainingEnrollmentController extends Controller
{
    public function index()
    {
        $query = TrainingEnrollment::with(['training', 'user']);

        if (request()->has('per_page')) {
            $enrollments = $query->paginate((int) request('per_page', 10));
        } else {
            $enrollments = $query->get();
        }

        return response()->json($enrollments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'user_id' => 'required|exists:users,id',
            'enrolled_on' => 'nullable|date',
            'status' => 'nullable|string',
        ]);

        $enrollment = TrainingEnrollment::create($validated);

        return response()->json($enrollment, 201);
    }

    public function show(TrainingEnrollment $trainingEnrollment)
    {
        return response()->json($trainingEnrollment->load('training', 'user'));
    }

    public function update(Request $request, TrainingEnrollment $trainingEnrollment)
    {
        $validated = $request->validate([
            'training_id' => 'sometimes|exists:trainings,id',
            'enrolled_on' => 'nullable|date',
            'status' => 'sometimes|integer',
        ]);

        $trainingEnrollment->update($validated);

        return response()->json([
            'message' => 'Training enrollment updated successfully.',
            'data' => $trainingEnrollment,
        ]);
    }

    public function destroy(TrainingEnrollment $trainingEnrollment)
    {
        $trainingEnrollment->delete();

        return response()->json(['message' => 'Enrollment deleted']);
    }
}

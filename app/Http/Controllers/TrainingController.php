<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $query = Training::query();

        if (request()->has('per_page')) {
            $perPage = (int) request('per_page', 10); // default 10
            $trainings = $query->paginate($perPage);
        } else {
            $trainings = $query->get();
        }

        return response()->json($trainings);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'software_id' => 'required|exists:softwares,id',
            'solution_id' => 'required|exists:softwares,id',
            'customer_id' => 'nullable|exists:customers,id',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $training = Training::create($validated);

        return response()->json($training, 201);
    }

    public function show(Training $training)
    {
        return response()->json($training->load('sessions', 'enrollments'));
    }

    public function update(Request $request, Training $training)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|integer',
        ]);

        $training->update($validated);

        return response()->json([
            'message' => 'Training updated successfully.',
            'data' => $training,
        ]);
    }

    public function destroy(Training $training)
    {
        $training->delete();

        return response()->json(['message' => 'Training deleted successfully']);
    }
}

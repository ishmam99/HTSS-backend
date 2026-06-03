<?php

namespace App\Http\Controllers;

use App\Models\TrainingCourse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternalTrainingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = TrainingCourse::with(['software', 'solution']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('analysis', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $trainings = $query->get();
            // ->latest()
            // ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Internal trainings fetched successfully.',
            'data' => $trainings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:training_courses,code'],
            'status' => ['nullable', 'integer', 'in:0,1,2'],

            'short_description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],

            'duration' => ['nullable', 'string', 'max:255'],

            'software_id' => ['nullable', 'exists:softwares,id'],
            'solution_id' => ['nullable', 'exists:solutions,id'],

            'level' => ['nullable', 'string', 'max:255'],

            'price' => ['nullable', 'numeric', 'min:0'],

            'type' => ['required', Rule::in(['onsite', 'online', 'hybrid'])],

            'analysis' => ['nullable', 'string', 'max:255'],
        ]);

        $training = TrainingCourse::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Internal training created successfully.',
            'data' => $training->load(['software', 'solution'])
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $training = TrainingCourse::with([
            'software',
            'solution'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Internal training fetched successfully.',
            'data' => $training
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $training = TrainingCourse::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],

            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('training_courses', 'code')->ignore($training->id)
            ],

            'status' => ['nullable', 'integer', 'in:0,1,2'],

            'short_description' => ['nullable', 'string'],
            'long_description' => ['nullable', 'string'],

            'duration' => ['nullable', 'string', 'max:255'],

            'software_id' => ['nullable', 'exists:softwares,id'],
            'solution_id' => ['nullable', 'exists:solutions,id'],

            'level' => ['nullable', 'string', 'max:255'],

            'price' => ['nullable', 'numeric', 'min:0'],

            'type' => ['required', Rule::in(['onsite', 'online', 'hybrid'])],

            'analysis' => ['nullable', 'string', 'max:255'],
        ]);

        $training->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Internal training updated successfully.',
            'data' => $training->load(['software', 'solution'])
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $training = TrainingCourse::findOrFail($id);

        $training->delete();

        return response()->json([
            'success' => true,
            'message' => 'Internal training deleted successfully.'
        ]);
    }
}
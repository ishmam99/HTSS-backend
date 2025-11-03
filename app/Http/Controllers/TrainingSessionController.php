<?php

namespace App\Http\Controllers;

use App\Models\TrainingSession;
use Illuminate\Http\Request;

class TrainingSessionController extends Controller
{
    public function index()
    {
        $query = TrainingSession::with('training');

        if (request()->has('per_page')) {
            $sessions = $query->paginate((int) request('per_page', 10));
        } else {
            $sessions = $query->get();
        }

        return response()->json($sessions);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'training_id' => 'required|exists:trainings,id',
            'session_title' => 'required|string|max:255',
            'session_date' => 'required|date',
            'location' => 'nullable|string|max:255',
        ]);

        $session = TrainingSession::create($validated);

        return response()->json($session, 201);
    }

    public function show(TrainingSession $trainingSession)
    {
        return response()->json($trainingSession->load('training'));
    }

    public function update(Request $request, TrainingSession $trainingSession)
    {
        $validated = $request->validate([
            'training_id' => 'sometimes|exists:trainings,id',
            'session_title' => 'sometimes|string|max:255',
            'session_date' => 'sometimes|date',
            'location' => 'nullable|string|max:255',
            'status' => 'sometimes|integer',
        ]);

        $trainingSession->update($validated);

        return response()->json([
            'message' => 'Training session updated successfully.',
            'data' => $trainingSession,
        ]);
    }

    public function destroy(TrainingSession $trainingSession)
    {
        $trainingSession->delete();

        return response()->json(['message' => 'Session deleted']);
    }
}

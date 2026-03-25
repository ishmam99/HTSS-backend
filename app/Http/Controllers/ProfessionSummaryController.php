<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProfessionSummaryRequest;
use App\Http\Resources\ProfessionSummaryResource;
use App\Models\ProfessionSummary;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfessionSummaryController extends Controller
{
    public function index(Request $request)
    {
        $summaries = ProfessionSummary::where('user_id', auth()->id())->first();

        return new ProfessionSummaryResource($summaries);
    }

    public function store(ProfessionSummaryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $summary = ProfessionSummary::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profession summary created successfully.',
            'data'    => $summary,
        ], 201);
    }

    public function show(ProfessionSummary $professionSummary): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profession summary fetched successfully.',
            'data'    => $professionSummary,
        ], 200);
    }

    public function update(ProfessionSummaryRequest $request, ProfessionSummary $professionSummary): JsonResponse
    {
        $validated = $request->validated();

        $professionSummary->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profession summary updated successfully.',
            'data'    => $professionSummary,
        ], 200);
    }

    public function destroy(ProfessionSummary $professionSummary): JsonResponse
    {
        $professionSummary->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profession summary deleted successfully.',
            'data'    => null,
        ], 200);
    }
}

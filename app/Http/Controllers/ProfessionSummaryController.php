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
        $summary = ProfessionSummary::where('user_id', auth()->id())->first();

        return response()->json([
            'success' => true,
            'data'    => $summary ? new ProfessionSummaryResource($summary) : null,
        ]);
    }

    public function store(ProfessionSummaryRequest $request): JsonResponse
    {
        $validated            = $request->validated();
        $validated['user_id'] = auth()->id();
        $summary              = ProfessionSummary::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profession summary created successfully.',
            'data'    => $summary,
        ], 201);
    }

    public function show(ProfessionSummary $professional_summary): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Profession summary fetched successfully.',
            'data'    => $professional_summary,
        ], 200);
    }

    public function update(ProfessionSummaryRequest $request, ProfessionSummary $professional_summary): JsonResponse
    {
        $validated = $request->validated();

        $professional_summary->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profession summary updated successfully.',
            'data'    => $professional_summary,
        ], 200);
    }

    public function destroy(ProfessionSummary $professional_summary): JsonResponse
    {
        $professional_summary->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profession summary deleted successfully.',
            'data'    => null,
        ], 200);
    }
}

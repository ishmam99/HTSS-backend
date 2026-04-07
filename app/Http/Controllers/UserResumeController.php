<?php
namespace App\Http\Controllers;

use App\Http\Resources\UserResumeResource;
use App\Models\UserResume;
use Illuminate\Http\Request;

class UserResumeController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (! $user->endUser) {
            return response()->json([
                'message' => 'End user not found.',
            ], 404);
        }
        $resumes = UserResume::with('endUser')->where('end_user_id', $user->endUser->id)
            ->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->get();
        return UserResumeResource::collection($resumes);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'    => 'required|string|max:255',
            'resume'   => 'required|array',
            'template' => 'required|string|max:255',
            'status'   => 'nullable',
        ]);

        $user = auth()->user();

        if (! $user->endUser) {
            return response()->json([
                'message' => 'End user not found.',
            ], 404);
        }

        $validated['end_user_id'] = $user->endUser->id;
        $resume                   = UserResume::create($validated);
        return response()->json([
            'status'  => true,
            'message' => 'Resume created successfully',
            'data'    => $resume,
        ], 201);
    }

    public function show(UserResume $user_resume)
    {
        $user_resume->load('endUser');
        return new UserResumeResource($user_resume);
    }

    public function update(Request $request, UserResume $user_resume)
    {
        $validated = $request->validate([
            'title'    => 'sometimes|string|max:255',
            'resume'   => 'sometimes|array',
            'template' => 'sometimes|string|max:255',
            'status'   => 'sometimes',
        ]);

        $user_resume->update($validated);
        return response()->json([
            'status'  => true,
            'message' => 'Resume updated successfully',
            'data'    => $user_resume,
        ], 200);
    }

    public function destroy(UserResume $user_resume)
    {
        $user_resume->delete();
        return response()->json([
            'status'  => true,
            'message' => 'Resume deleted successfully',
        ], 200);
    }
}

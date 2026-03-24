<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserExperienceRequest;
use App\Http\Resources\UserExperienceResource;
use App\Models\UserExperience;

class UserExperienceController extends Controller
{
    public function index()
    {
        $userExperience = UserExperience::where('user_id', auth()->id())->get();
        return UserExperienceResource::collection($userExperience);
    }

    public function store(UserExperienceRequest $request)
    {
        $validated            = $request->validated();
        $validated['user_id'] = auth()->id();
        $userExperience       = UserExperience::create($validated);
        return response()->json([
            'status'  => true,
            'message' => 'User experience created successfully',
            'data'    => $userExperience,
        ], 201);
    }

    public function show(UserExperience $user_experience)
    {
        return new UserExperienceResource($user_experience);
    }

    public function update(UserExperienceRequest $request, UserExperience $user_experience)
    {
        $user_experience->update($request->validated());
        return response()->json([
            'status'  => true,
            'message' => 'User experience updated successfully',
            'data'    => $user_experience,
        ], 200);
    }

    public function destroy(UserExperience $user_experience)
    {
        $user_experience->delete();
        return response()->json([
            'status'  => true,
            'message' => 'User experience deleted successfully',
        ], 200);
    }
}

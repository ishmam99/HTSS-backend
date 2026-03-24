<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserEducationRequest;
use App\Http\Resources\UserEducationResource;
use App\Models\UserEducation;

class UserEducationController extends Controller
{
    public function index()
    {
        $userEducation = UserEducation::where('user_id', auth()->id())->get();
        return UserEducationResource::collection($userEducation);
    }

    public function store(UserEducationRequest $request)
    {
        $validated            = $request->validated();
        $validated['user_id'] = auth()->id();
        $userEducation        = UserEducation::create($validated);
        return new UserEducationResource($userEducation);
    }

    public function show(UserEducation $user_education)
    {
        return new UserEducationResource($user_education);
    }

    public function update(UserEducationRequest $request, UserEducation $user_education)
    {
        $user_education->update($request->validated());
        return new UserEducationResource($user_education);
    }

    public function destroy(UserEducation $user_education)
    {
        $user_education->delete();
        return response()->json([
            'status'  => true,
            'message' => 'User education deleted successfully',
        ], 200);
    }
}

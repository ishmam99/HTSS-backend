<?php

namespace App\Http\Controllers;

use App\Models\EndUser;
use App\Http\Requests\EndUserRequest;
use App\Http\Resources\EndUserResource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EndUserController extends Controller
{
    public function index(Request $request)
    {
        $query = EndUser::when('status', function($query, $request) {
            return $query->where('status', $request->status);
        })->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return EndUserResource::collection($lists);
    }


   public function store(EndUserRequest $request)
    {
        $data = $request->validated();

       
        $userData = [
            'name' => $request->username ?? 'EndUser', 
            'email' => $request->email ?? 'user'.time().'@example.com', 
            'password' => Hash::make($request->password ?? '12345678'),
        ];
        $user = User::create($userData);
        $data['user_id'] = $user->id;
        $endUser = EndUser::create($data);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('uploads/endUser', 'public');
            $endUser->update(['image' => $path]);
        }
        return response()->json([
            'status' => true,
            'message' => 'EndUser created successfully',
            'data' => $endUser
        ], 201);
    }

    public function show(EndUser $endUser)
    {
        return new EndUserResource($endUser);
    }

    public function update(EndUserRequest $request, EndUser $endUser)
    {
        $data = $request->validated();
        if ($request->filled('password')) {
            $endUser->user->update([
                'password' => bcrypt($request->password),
            ]);
        }
        if ($request->hasFile('image')) {
            if ($endUser->image && Storage::disk('public')->exists($endUser->image)) {
                Storage::disk('public')->delete($endUser->image);
            }
            $path = $request->file('image')->store('uploads/endUser', 'public');
            $data['image'] = $path;
        }
        $endUser->update($data);
        return response()->json([
            'status' => true,
            'message' => 'EndUser updated successfully',
            'data' => $endUser
        ], 200);
    }

    public function destroy(EndUser $endUser)
    {
        if ($endUser->image && Storage::disk('public')->exists($endUser->image)) {
            Storage::disk('public')->delete($endUser->image);
        }
        User::where('id', $endUser->user_id)->delete();
        $endUser->delete();
        return response()->json(['status' => true,'message' => 'EndUser deleted successfully'],200);
    }
}

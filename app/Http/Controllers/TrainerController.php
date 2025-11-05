<?php

namespace App\Http\Controllers;

use App\Models\Trainer;
use App\Http\Requests\TrainerRequest;
use App\Http\Resources\TrainerResource;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TrainerController extends Controller
{
    public function index(Request $request)
    {
        $query = Trainer::when('status', function($query, $request) {
            return $query->where('status', $request->status);
        })->orderBy('id', 'desc');

        if ($request->has('per_page')) {
            $lists = $query->paginate($request->per_page);
        } else {
            $lists = $query->get();
        }

        return TrainerResource::collection($lists);
    }


    public function store(TrainerRequest $request)
{
    $data = $request->validated();

    $user = User::create([
        'name' => $data['name'],
        'email' => $data['email'],
        'password' => Hash::make($data['password'] ?? '12345678'),
    ]);

    $data['user_id'] = $user->id;
    $trainer = Trainer::create($data);

    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('uploads/trainer', 'public');
        $trainer->update(['image' => $path]);
    }

    return response()->json([
        'status' => true,
        'message' => 'Trainer created successfully',
        'data' => $trainer
    ], 201);
}

    public function show(Trainer $trainer)
    {
        return new TrainerResource($trainer);
    }

    public function update(TrainerRequest $request, Trainer $trainer)
    {
        $data = $request->validated();

        if ($request->filled('password')) {
            $trainer->user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        if ($request->hasFile('image')) {
            if ($trainer->image && Storage::disk('public')->exists($trainer->image)) {
                Storage::disk('public')->delete($trainer->image);
            }

            $path = $request->file('image')->store('uploads/trainer', 'public');
            $data['image'] = $path;
        }

        $trainer->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Trainer updated successfully',
            'data' => $trainer
        ], 200);
    }

    public function destroy(Trainer $trainer)
    {
        if ($trainer->image && Storage::disk('public')->exists($trainer->image)) {
            Storage::disk('public')->delete($trainer->image);
        }
        $trainer->delete();
        return response()->json(['status' => true,'message' => 'Trainer deleted successfully'],200);
    }
}
